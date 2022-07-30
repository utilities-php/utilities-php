import crypto from "Crypto";

const encrypt = function (plain_text, encryptionMethod, secret, iv) {
    const encryptor = crypto.createCipheriv(encryptionMethod, secret, iv);
    const data = encryptor.update(plain_text, 'utf8', 'base64') + encryptor.final('base64');
    return btoa(data);
};

const decrypt = function (encryptedMessage, encryptionMethod, secret, iv) {
    const input = atob(encryptedMessage);
    const decryptor = crypto.createDecipheriv(encryptionMethod, secret, iv);
    return decryptor.update(input, 'base64', 'utf8') + decryptor.final('utf8');
};

const textToEncrypt = "Abcdefghijklmnopqrstuvwxyz";
const encryptionMethod = 'AES-256-CBC';
const secret = "My32charPasswordAndInitVectorStr";
const iv = secret.substr(0, 16);

const encryptedMessage = encrypt(
    textToEncrypt,
    encryptionMethod,
    secret,
    iv
);

const decryptedMessage = decrypt(
    encryptedMessage,
    encryptionMethod,
    secret,
    iv
);

console.log(encryptedMessage);
console.log(decryptedMessage);
