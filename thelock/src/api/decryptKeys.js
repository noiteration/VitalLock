const express = require('express');
const sss = require('shamirs-secret-sharing')

const router = express.Router();

router.post('/', (req, res) => {
  const { part1, part2 } = req.body;

  try {
    // Decode the parts
    const partial1 = part1;
    const partial2 = part2;
    const recovered_secret = sss.combine([partial1, partial2])
console.log(recovered_secret.toString())

    res.json({ message: recovered_secret.toString() });
  } catch (error) {
    console.error('Error decrypting private key:', error);
    res.status(400).json({ error: 'Failed to decrypt private key' });
  }
});

module.exports = router;
