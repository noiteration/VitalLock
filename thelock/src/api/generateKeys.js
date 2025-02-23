const express = require("express");
const crypto = require("crypto");
const sss = require("shamirs-secret-sharing");

const PARTS = 4;
const QUORUM = 2;

const router = express.Router();

// Function to generate a public-private key pair quantum proof
async function generateKeyPair() {
	// const theKeys = ml_kem768.keygen();
	// const publicKey = theKeys.publicKey;
	// const privateKey = theKeys.secretKey;
	// resolve({ publicKey, privateKey });
	return new Promise((resolve, reject) => {
		crypto.generateKeyPair(
			"rsa",
			{
				modulusLength: 2048,
				publicKeyEncoding: {
					type: "spki",
					format: "pem",
				},
				privateKeyEncoding: {
					type: "pkcs8",
					format: "pem",
				},
			},
			(err, publicKey, privateKey) => {
				if (err) {
					reject(err);
				} else {
					resolve({ publicKey, privateKey });
				}
			}
		);
	});
}

router.post("/", async (req, res) => {
	try {
		// encryption using ml-kem768
		const { publicKey, privateKey } = await generateKeyPair();
		const secretBuffer = Buffer.from(privateKey);
		const parts = sss.split(secretBuffer, { shares: PARTS, threshold: QUORUM });

		const idKey = parts[0].toString("hex");
		const secretKey = parts[1].toString("hex");
		const healthCareKey = parts[2].toString("hex");
		const emergencyContactKey = parts[3].toString("hex");

		res.json({
			publicKey: publicKey,
			idKey: idKey,
			secretKey: secretKey,
			healthCareKey: healthCareKey,
			emergencyContactKey: emergencyContactKey,
		});
	} catch (error) {
		console.error("Error generating key pair:", error);
		res.status(500).json({ error: "Internal server error" });
	}
});

module.exports = router;
