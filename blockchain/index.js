const express = require('express');
const bodyParser = require('body-parser');
const crypto = require('crypto');
const fs = require('fs');

class Block {
  constructor(index, timestamp, data, previousHash = '') {
    this.index = index;
    this.timestamp = timestamp;
    this.data = data;
    this.previousHash = previousHash;
    this.hash = this.calculateHash();
  }

  calculateHash() {
    return crypto.createHash('sha256')
      .update(this.index + this.previousHash + this.timestamp + JSON.stringify(this.data))
      .digest('hex');
  }
}

class Blockchain {
  constructor() {
    this.chain = this.loadChain();
    if (this.chain.length === 0) {
      this.chain.push(this.createGenesisBlock());
      this.saveChain();
    }
  }

  createGenesisBlock() {
    return new Block(0, "2025-02-23", "Genesis block", "0");
  }

  getLatestBlock() {
    return this.chain[this.chain.length - 1];
  }

  addBlock(data) {
    const newBlock = new Block(this.chain.length, new Date().toISOString(), data, this.getLatestBlock().hash);
    this.chain.push(newBlock);
    this.saveChain();
    return newBlock;
  }

  loadChain() {
    try {
      const data = fs.readFileSync('blockchain.json', 'utf8');
      return JSON.parse(data);
    } catch (error) {
      return [];
    }
  }

  saveChain() {
    fs.writeFileSync('blockchain.json', JSON.stringify(this.chain, null, 2), 'utf8');
  }

  isChainValid() {
    for (let i = 1; i < this.chain.length; i++) {
      const currentBlock = this.chain[i];
      const previousBlock = this.chain[i - 1];

      if (currentBlock.hash !== currentBlock.calculateHash()) {
        return false;
      }

      if (currentBlock.previousHash !== previousBlock.hash) {
        return false;
      }
    }
    return true;
  }
}

const app = express();
app.use(bodyParser.json());

const blockchain = new Blockchain();

app.get('/blockchain', (req, res) => {
  res.json(blockchain.chain);
});

app.post('/add-block', (req, res) => {
  const { data } = req.body;
  const newBlock = blockchain.addBlock(data);
  res.json({ message: "Block added successfully", block: newBlock });
});

const PORT = 3002;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));
