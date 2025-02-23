const express = require("express");
const crypto = require("crypto");
const { split, join } = require("shamir");
const { randomBytes } = require("crypto");

const PARTS = 4;
const QUORUM = 2;

const router = express.Router();

// Function to generate a public-private key pair
async function generateKeyPair() {
	return new Promise((resolve, reject) => {
		crypto.generateKeyPair(
			"rsa",
			{
				modulusLength: 512,
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
		// moot security measure
		const { secret } = req.body;
		if (!secret) {
			return res.status(400).json({ error: "Maybe send a payload too." });
		}

		// encryption using ml-kem768
		const { publicKey, privateKey } = await generateKeyPair();
		const utf8Encoder = new TextEncoder();
		const secretBytes = utf8Encoder.encode(privateKey);
		const parts = split(randomBytes, PARTS, QUORUM, secretBytes);
		console.log(parts);

		const regularArray = Array.from(parts[2]);
		const jsonString = JSON.stringify(regularArray);
		const retrievedArray = JSON.parse(jsonString);
		const uint8Array = new Uint8Array(retrievedArray);
		const partsToJoin = {
			1: parts[2],
			4: parts[4],
		};

		console.log(partsToJoin);
		res.json({ partsToJoin });
	} catch (error) {
		console.error("Error generating key pair:", error);
		res.status(500).json({ error: "Internal server error" });
	}
});

module.exports = router;
