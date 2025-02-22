const express = require('express');
const router = express.Router();

router.post('/', (req, res) => {
  const { secret } = req.body;
  
  if (!secret) {
    return res.status(400).json({ error: 'Parameter is required' });
  }

  const result = secret;
  res.json({ result });
});

module.exports = router;