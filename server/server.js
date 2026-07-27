// ==============================================================================
// 1. MD. Tawhidul Islam - Team Lead (Branch: feature/server-init)
// Target File: server/server.js
// Exact Requirements:
//  - Initialize Node.js environment.
//  - Configure Express server to serve static files from the /public directory.
//  - Setup JSON body parsing.
// ==============================================================================

const express = require('express');
const path = require('path');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

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
