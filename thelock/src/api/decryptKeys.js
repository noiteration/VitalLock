const express = require('express');
const crypto = require('crypto');
const { join } = require('shamir');

const router = express.Router();

router.post('/', (req, res) => {
  const { part1, part2 } = req.body;

  try {
    // Decode the parts
    const decodedPart1 = Buffer.from(part1, 'base64');
    const decodedPart2 = Buffer.from(part2, 'base64');

    // Reconstruct the secret
    const reconstructedSecret = join({
      1: decodedPart1,
      2: decodedPart2
    });

    // Decrypt the private key
    const privateKey = crypto.createPrivateKey({
      key: reconstructedSecret,
      format: 'der',
      type: 'pkcs8'
    });

    // Convert the private key to PEM format
    const decodedPrivateKey = privateKey.export({
      type: 'pkcs8',
      format: 'pem'
    });

    // Send the decoded private key as the response
    res.json({ decodedPrivateKey });
  } catch (error) {
    console.error('Error decrypting private key:', error);
    res.status(400).json({ error: 'Failed to decrypt private key' });
  }
});

module.exports = router;
