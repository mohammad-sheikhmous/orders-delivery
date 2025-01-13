<!DOCTYPE html>
<html>
<head>
    <title>Firebase Notification Example</title>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
</head>
<body>
<h1>Firebase Notification Example</h1>

<script>
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
    const firebaseConfig = {
        apiKey: "AIzaSyCUgJWEf5w3vnz3ltytjnsTMYW-PzY_X6w",
        authDomain: "oreders-delivery.firebaseapp.com",
        projectId: "oreders-delivery",
        storageBucket: "oreders-delivery.firebasestorage.app",
        messagingSenderId: "748752647385",
        appId: "1:748752647385:web:4d15773a5425fa8341fac8",
        measurementId: "G-C02XJQZG2E"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    // Get a reference to the messaging service
    const messaging = firebase.messaging();

    // Request permission for notifications
    messaging.requestPermission()
        .then(function() {
            console.log('Notification permission granted.');
            return messaging.getToken();
        })
        .then(function(token) {
            console.log('FCM Token:', token);
            //sendTokenToServer(token); // Send the token to your server
        })
        .catch(function(err) {
            console.error('Unable to get permission to notify.', err);
        });

    // Handle token refresh
    messaging.onTokenRefresh(function() {
        messaging.getToken()
            .then(function(refreshedToken) {
                console.log('Token refreshed:', refreshedToken);
                sendTokenToServer(refreshedToken); // Send the refreshed token to your server
            })
            .catch(function(err) {
                console.error('Unable to refresh token.', err);
            });
    });

    // Function to send the token to your server
    function sendTokenToServer(token) {
        // ... (Use AJAX or Fetch API to send the token)
    }
</script>
</body>
</html>
