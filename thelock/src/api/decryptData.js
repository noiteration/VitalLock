const express = require("express");
const crypto = require("crypto");

const router = express.Router();

router.post("/", (req, res) => {
    const { encryptedData, privateKey } = req.body;

    if (!encryptedData || !privateKey) {
        return res.status(400).json({ error: "Missing encryptedData or privateKey" });
    }

    try {
        // Convert the base64 encoded encrypted data back to a buffer
        const bufferEncryptedData = Buffer.from(encryptedData, 'base64');

        const decryptedData = crypto.privateDecrypt(
            {
                key: privateKey,
                padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
                oaepHash: "sha256"
            },
            bufferEncryptedData
        );
        
        res.json({ "result": decryptedData.toString('utf8') });
    } catch (error) {
        console.error("Decryption error:", error);
        res.status(500).json({ error: "Decryption failed", details: error.message });
    }
});

module.exports = router;
