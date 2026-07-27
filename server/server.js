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
// ==============================================================================

const express = require('express');
const path = require('path');
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
