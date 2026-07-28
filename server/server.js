// ==============================================================================
// 1. MD. Tawhidul Islam - Team Lead (Branch: feature/server-init)
// 2. Safa (Branch: feature/security-auth)
// 3. Afia Lubna Purnota (Branch: feature/database-config)
// 4. Hiya Moni (Branch: feature/api-routes)
// Target File: server/server.js
// Exact Requirements:
//  - Initialize Express server serving static files from /public and root directory.
//  - Security middleware: helmet HTTP headers & 1,000 req / 15 min express-rate-limit.
//  - Route connection: Mount Hiya's authRoutes router.
//  - Integrated PHP Execution Handler via php-cgi for .php scripts.
// ==============================================================================

const express = require('express');
const path = require('path');
const fs = require('fs');
const { spawn } = require('child_process');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
require('dotenv').config();

const authRoutes = require('./routes/authRoutes');

const app = express();
const PORT = process.env.PORT || 3000;

// Security Middleware: Helmet HTTP Headers
app.use(helmet({
    contentSecurityPolicy: false // Allows CDN assets (Tailwind, FontAwesome) to load in public/
}));

// Security Middleware: Express Rate Limiter capped at 1,000 requests per 15 minutes
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 1000,
    standardHeaders: true,
    legacyHeaders: false,
    message: { error: 'Too many requests from this IP, please try again after 15 minutes.' }
});
app.use(limiter);

// JSON and URL-encoded body parsing
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Mount Auth API Routes (Hiya Moni's task)
app.use('/api/auth', authRoutes);
app.use('/api', authRoutes);

// Dedicated PHP CGI Execution Handler for .php requests
app.all('*.php', (req, res) => {
    const scriptPath = path.join(__dirname, '..', req.path);
    if (!fs.existsSync(scriptPath)) {
        return res.status(404).send('PHP script not found: ' + req.path);
    }

    let bodyStr = '';
    if (req.method === 'POST' || req.method === 'PUT' || req.method === 'PATCH') {
        if (req.body && typeof req.body === 'object') {
            bodyStr = new URLSearchParams(req.body).toString();
        } else if (typeof req.body === 'string') {
            bodyStr = req.body;
        }
    }

    const env = Object.assign({}, process.env, {
        REDIRECT_STATUS: '200',
        REQUEST_METHOD: req.method,
        SCRIPT_FILENAME: scriptPath,
        SCRIPT_NAME: req.path,
        PATH_INFO: req.path,
        QUERY_STRING: req.url.includes('?') ? req.url.split('?')[1] : '',
        CONTENT_TYPE: req.headers['content-type'] || 'application/x-www-form-urlencoded',
        CONTENT_LENGTH: Buffer.byteLength(bodyStr).toString(),
        HTTP_COOKIE: req.headers['cookie'] || '',
        HTTP_HOST: req.headers['host'] || '',
        HTTP_USER_AGENT: req.headers['user-agent'] || '',
        HTTP_ACCEPT: req.headers['accept'] || '',
        SERVER_PROTOCOL: 'HTTP/1.1',
        SERVER_PORT: (process.env.PORT || '3000').toString()
    });

    const php = spawn('php-cgi', [], { env });
    let output = Buffer.alloc(0);
    let errorOutput = '';

    php.stdout.on('data', (data) => {
        output = Buffer.concat([output, data]);
    });

    php.stderr.on('data', (data) => {
        errorOutput += data.toString();
    });

    php.on('close', (code) => {
        if (output.length === 0) {
            console.error('PHP CGI error:', errorOutput);
            return res.status(500).send('Error executing PHP script.');
        }

        let headerEndIndex = output.indexOf('\r\n\r\n');
        let delimiterLength = 4;
        if (headerEndIndex === -1) {
            headerEndIndex = output.indexOf('\n\n');
            delimiterLength = 2;
        }

        let headersRaw = '';
        let body = output;

        if (headerEndIndex !== -1) {
            headersRaw = output.slice(0, headerEndIndex).toString('utf8');
            body = output.slice(headerEndIndex + delimiterLength);
        }

        const lines = headersRaw.split(/\r?\n/);
        let statusCode = 200;

        lines.forEach(line => {
            if (!line.trim()) return;
            const colonIdx = line.indexOf(':');
            if (colonIdx === -1) return;
            const headerName = line.slice(0, colonIdx).trim();
            const headerValue = line.slice(colonIdx + 1).trim();

            if (headerName.toLowerCase() === 'status') {
                const match = headerValue.match(/^(\d+)/);
                if (match) statusCode = parseInt(match[1], 10);
            } else if (headerName.toLowerCase() === 'set-cookie') {
                res.append('Set-Cookie', headerValue);
            } else {
                res.setHeader(headerName, headerValue);
            }
        });

        res.status(statusCode).send(body);
    });

    if (bodyStr) {
        php.stdin.write(bodyStr);
    }
    php.stdin.end();
});

// Serve static files from the /public directory and workspace root
app.use(express.static(path.join(__dirname, '../public')));
app.use(express.static(path.join(__dirname, '..')));

// Root route fallback to static SPA
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '../public/index.html'));
});

// Explicit route for home.html
app.get('/home.html', (req, res) => {
    res.sendFile(path.join(__dirname, '../home.html'));
});

// Start Express server
if (require.main === module) {
    app.listen(PORT, () => {
        console.log(`UniMarket Express Server running on port ${PORT}`);
    });
}

module.exports = app;
