// ==============================================================================
// 1. MD. Tawhidul Islam - Team Lead (Branch: feature/server-init)
// 2. Safa (Branch: feature/security-auth)
// Target File: server/server.js
// Exact Requirements:
//  - Initialize Node.js environment & Express server serving static files from /public.
//  - Setup JSON body parsing.
//  - Integrate security middleware: helmet for HTTP headers.
//  - Integrate security middleware: express-rate-limit capped at 1,000 requests per 15 minutes.
// ==============================================================================

const express = require('express');
const path = require('path');
const helmet = require('helmet');
const rateLimit = require('express-rate-limit');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Security Middleware: Helmet HTTP Headers
app.use(helmet({
    contentSecurityPolicy: false // Allows CDN assets (Tailwind, FontAwesome) to load in public/
}));

// Security Middleware: Express Rate Limiter capped at 1,000 requests per 15 minutes
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 1000, // Capped at 1,000 requests per 15 minutes
    standardHeaders: true,
    legacyHeaders: false,
    message: { error: 'Too many requests from this IP, please try again after 15 minutes.' }
});
app.use(limiter);

// JSON and URL-encoded body parsing
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve static files from the /public directory
app.use(express.static(path.join(__dirname, '../public')));

// Root route fallback to static SPA
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, '../public/index.html'));
});

// Start Express server
if (require.main === module) {
    app.listen(PORT, () => {
        console.log(`UniMarket Express Server running on port ${PORT}`);
    });
}

module.exports = app;
