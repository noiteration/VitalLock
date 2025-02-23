const express = require('express');
const morgan = require('morgan');
const helmet = require('helmet');
const cors = require('cors');

const generateKeys = require('./api/generateKeys')
const decryptKeys = require('./api/decryptKeys')
const encryptData = require('./api/encryptData')
const decryptData = require('./api/decryptData')
const refreshKeys = require('./api/refreshKeys')


require('dotenv').config();

const middlewares = require('./middlewares');
const api = require('./api');

const app = express();

app.use(morgan('dev'));
app.use(helmet());
app.use(cors());
app.use(express.json());

app.get('/', (req, res) => {
  res.json({
    message: 'You made it here great',
  });
});

app.use('/api/v1', api);
app.use('/api/generatekeys', generateKeys);
app.use('/api/decryptkeys', decryptKeys);
app.use('/api/encryptdata', encryptData);
app.use('/api/decryptdata', decryptData);
app.use('/api/refreshkeys', refreshKeys)

app.use(middlewares.notFound);
app.use(middlewares.errorHandler);

module.exports = app;
