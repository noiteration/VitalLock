const express = require("express");
const sss = require("shamirs-secret-sharing");

const PARTS = 4;
const QUORUM = 2;

const router = express.Router();

router.post("/", async (req, res) => {
	try {
		const { privateKey } = req.body;
		const secretBuffer = Buffer.from(privateKey);
		const parts = sss.split(secretBuffer, { shares: PARTS, threshold: QUORUM });

		const idKey = parts[0].toString("hex");
		const secretKey = parts[1].toString("hex");
		const healthCareKey = parts[2].toString("hex");
		const emergencyContactKey = parts[3].toString("hex");

		res.json({
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
