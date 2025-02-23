# Vital Lock

Vital Lock is a privacy-focused health passport and is focused on upholding the principles of web3. It obscures users true identity while maintaining all their data back to them.

Shamir's Secret Sharing Algorithm sits at the center of the program creating 4 shares of the private keys which will require atleast 2 to be combined. One is kept by the health care provider. The other 3 are provided to the user where one is supposed to be given to an emergency contact in case of emergencies (duh!).

Any 2 of the keys can be used to access the encrypted data meaning you can always view your data but your emergency contact cannot access your data by their key alone and neither can the health care organization. Maybe your crazy ex-partner was a co-worker of your psychologist and now knows all about your therapy sessions because she has access to all information. Worry no more. They can only view gibberish text and not even know if data is yours. Data has been protected at multiple levels and identity has been obfuscated as well in order to maintain a secure level of privacy.

Zero-Knowledge Proofs have been set up as well which can be applied in many different cases like showing your health eligibility, your vision eligibility (demonstrated in the MTO section) etcetera.ML-KEM-768 is said to be quantum proof encryption. The program has been set to use the lattice based encryption for the best encryption of the data. RSA has been used for key generation as of now with all the mechanisms in place to handle the QPE.

# Running in development mode
- Ensure NodeJS is installed and is properly working in your system.

## The Laravel Side of things
- Laravel was utilized to manage and deploy database. It was primarily chosen for better manageability and faster deployment because of its 'batteries included' 
- Make sure PHP, MySQL Server (these two from XAMPP or individually installed) and Composer are installed in your system.
- Run `composer install` in the `frontend` folder.
- Whilst it sets up, create a database named `<whatever you heart feels like>` on your MySQL Server. Just make sure to have the same on your .env file as well.
- Make a copy of `.env.example` and name it `.env` so that a environment file is created.
- Run `php artisan migrate:fresh` to run migrations and send all the data to the database.

- In the `/frontend` directory (we know it's not a frontend only but anyways), run `npm install` and a quick `npm run build` to install required node modules and build the vite files respectively.
- Run `php artisan serve` to get it up and working.
- The project should be up and running at `https://localhost:8000` by default or different port/url depending upon your settings.

## The Blockchain Simulation
- A simple data store blockchain simulation has been built to simulate the mainnet. We can properly see the blockchain in action through a page as well.
- To run the blockchain, head over to the blockchain folder.
- To run the application in  development mode, run `npm install` from the directory to install required node modules.
- Then, from the directory itself, run `node index.js`.
- This runs in `http://localhost:3002`.

## The Lock (API doing all the heavy lifting)
- The express server that handles all the encryption and decryption via APIs.
- Head over to the `thelock` directory.
- Do note that there are certain parameters required to run some functions, so do keep the parameters in check and wait for the API Docs to be released (Soon, we promise!!). The files or the functions are inside the `/api` folder.
- To run the application in  development mode, run `npm install` from the directory to install required node modules.
- Then, from the directory itself, run `npm run dev`.
- This runs in `http://localhost:3001`.

## Development
Was fun building the project. we are grateful for the mentors and peers that supported us throughout the Hackathon. While lack of time has certainly caused so many things yet to be improved, the functionality is more than a proof-of-concept for some concepts that we have applied and wanted to apply as well. Feel free to hack your changes on the repo (after the hackathon!!). Any and all help is appreciated.