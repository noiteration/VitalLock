const express = require("express");
const crypto = require("crypto");

const router = express.Router();

router.post("/", (req, res) => {
    const { data, publicKey } = req.body;

    if (!data || !publicKey) {
        return res.status(400).json({ error: "Missing data or publicKey" });
    }

    try {
        const bufferData = Buffer.from(data, "utf8");
        const encryptedData = crypto.publicEncrypt(
            {
                key: publicKey,
                padding: crypto.constants.RSA_PKCS1_OAEP_PADDING,
                oaepHash: "sha256"
            },
            bufferData
        );
        
        res.json({ "result": encryptedData.toString('base64') });
    } catch (error) {
        console.error("Encryption error:", error);
        res.status(500).json({ error: "Encryption failed", details: error.message });
    }
});

module.exports = router;
