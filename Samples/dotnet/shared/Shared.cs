﻿using System;
using System.IO;
using System.Security.Cryptography;
using System.Text;

namespace PayJS_Samples.Misc
{
    public static class Shared
    {
        // sign up at https://developer.sagepayments.com/ to get your own dev creds
        public static string DeveloperID = "7SMmEF02WyC7H5TSdG1KssOQlwOOCagb";
        public static string DeveloperKEY = "wtC5Ns0jbtiNA8sP";

        // this is a shared test account; don't hesitate to ask us for one of your own!
        public static string MerchantID = "999999999997";
        public static string MerchantKEY = "K3QD6YWyhfD";

        public static string Environment = "cert";
        public static string PostbackUrl = "https://www.example.com/myHandler.php"; // https://requestb.in is great for playing with this
        public static string Amount = "1.00"; // use 5.00 to simulate a decline
        public static string PreAuth = "false";

        public static string GetAuthKey(string toHash, string privateKey, byte[] iv, string salt)
        {
            toHash = UTF8Encoding.UTF8.GetString(UTF8Encoding.UTF8.GetBytes(toHash));
            string passphrase = privateKey;

            byte[] encryptedResult;
            using (Aes aesAlg = Aes.Create())
            {
                using (Rfc2898DeriveBytes pbkdf2 = new Rfc2898DeriveBytes(passphrase, UTF8Encoding.UTF8.GetBytes(salt), 1500))
                {
                    aesAlg.Key = pbkdf2.GetBytes(32);
                }
                aesAlg.IV = iv;
                aesAlg.Padding = PaddingMode.PKCS7;

                ICryptoTransform encryptor = aesAlg.CreateEncryptor(aesAlg.Key, aesAlg.IV);

                using (MemoryStream msEncrypt = new MemoryStream())
                {
                    using (CryptoStream csEncrypt = new CryptoStream(msEncrypt, encryptor, CryptoStreamMode.Write))
                    {
                        using (StreamWriter swEncrypt = new StreamWriter(csEncrypt))
                        {
                            swEncrypt.Write(toHash);
                        }
                        encryptedResult = msEncrypt.ToArray();
                    }
                }
            }
            ;
            return Convert.ToBase64String(encryptedResult);
        }

        public static Nonces GetNonces()
        {
            byte[] nonceBytes = new byte[16];
            using (RNGCryptoServiceProvider rng = new RNGCryptoServiceProvider())
            {
                rng.GetBytes(nonceBytes);
            }
            var result = new Nonces();
            result.IV = nonceBytes;
            result.Salt = Convert.ToBase64String(UTF8Encoding.UTF8.GetBytes(BytesToHex(nonceBytes)));
            return result;
        }

        private static string BytesToHex(byte[] ba)
        {
            StringBuilder hex = new StringBuilder(ba.Length * 2);
            foreach (byte b in ba)
                hex.AppendFormat("{0:x2}", b);
            return hex.ToString();
        }
    }

    public class Nonces
    {
        public byte[] IV;
        public string Salt;
    }
}