// ==============================================================================
// 4. Hiya Moni (Branch: feature/api-routes)
// Target File: server/routes/authRoutes.js
// Exact Requirements:
//  - Map the Express router endpoints (POST /register, POST /login) to Safa's controller functions
//  - Connect them to the main server.js file.
// ==============================================================================

const express = require('express');
const router = express.Router();
const { register, login } = require('../controllers/authController');

// Map POST /register endpoint to Safa's register controller function
router.post('/register', register);

// Map POST /login endpoint to Safa's login controller function
router.post('/login', login);

module.exports = router;
