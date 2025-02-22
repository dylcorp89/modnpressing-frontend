export const firebaseConfig = {
    apiKey: "AIzaSyAuqy...",
    authDomain: "modn-pressing-d48f9.firebaseapp.com",
    databaseURL: "https://modn-pressing-d48f9-default-rtdb.firebaseio.com",
    projectId: "modn-pressing-d48f9",
    storageBucket: "modn-pressing-d48f9.appspot.com",
    messagingSenderId: "645101488086",
    appId: "1:645101488086:web:336a734151659f195f4677",
    measurementId: "G-E4CGQLNBQB"
};

// Initialise Firebase
firebase.initializeApp(firebaseConfig);

// Références à la base de données
export const dbRef = firebase.database().ref('commandes');
export const dbRefUser = firebase.database().ref('users');
