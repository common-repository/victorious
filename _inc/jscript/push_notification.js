
var config = {
    apiKey: wpfs['setting_victorious_firebase_apikey'],
    //authDomain: "web-n-e639c.firebaseapp.com",
    //databaseURL: "https://web-n-e639c.firebaseio.com",
    //storageBucket: "<BUCKET>.appspot.com",
    messagingSenderId: wpfs['setting_victorious_firebase_senderid'],
};
firebase.initializeApp(config);

const messaging = firebase.messaging();

subscribeFCM();
function subscribeFCM() {
    messaging.requestPermission()
        .then(function () {
            console.log("Notification permission granted.");
            showToken();
        })
        .catch(function (err) {
            console.log("Unable to get permission to notify. ", err);
            unSubcribePushNotification();
        });
}

function showToken() {
    // Get Instance ID token. Initially this makes a network call, once retrieved
    // subsequent calls to getToken will return from cache.
    messaging.getToken()
        .then(function (currentToken) {
            if (currentToken) {
                console.log("currentToken", currentToken);
                subcribePushNotification(currentToken);
            } else {
                // Show permission request.
                console.log("No Instance ID token available. Request permission to generate one.");
                unSubcribePushNotification();
            }
        })
        .catch(function (err) {
            console.log("An error occurred while retrieving token. ", err);
            unSubcribePushNotification();
        });
}

messaging.onTokenRefresh(function () {
    messaging.getToken()
        .then(function (refreshedToken) {
            console.log("Token refreshed.");
            subcribePushNotification(refreshedToken);
        })
        .catch(function (err) {
            console.log("Unable to retrieve refreshed token ", err);
            unSubcribePushNotification();
        });
});

function subcribePushNotification(token)
{
    var params = {
        action: 'subscribePushNotification',
        token: token
    };
    jQuery.post(ajaxurl, params, function (result) {
    });
}

function unSubcribePushNotification()
{
    var params = {
        action: 'unSubcribePushNotification',
    };
    jQuery.post(ajaxurl, params, function (result) {
    });
}