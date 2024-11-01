// Give the service worker access to Firebase Messaging.
// Note that you can only use Firebase Messaging here, other Firebase libraries
// are not available in the service worker.
importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js');

// Initialize the Firebase app in the service worker by passing in the
// messagingSenderId.
var config = {
    apiKey: "AIzaSyCQMZWTZ8WIcgSLyzDK_0mYqrALnyTKBzs",
    //authDomain: "web-n-e639c.firebaseapp.com",
    //databaseURL: "https://web-n-e639c.firebaseio.com",
    //storageBucket: "<BUCKET>.appspot.com",
    messagingSenderId: "681656501096",
};
firebase.initializeApp(config);

// Retrieve an instance of Firebase Messaging so that it can handle background
// messages.
const messaging = firebase.messaging();


/*messaging.setBackgroundMessageHandler(function(payload) {
 console.log('[firebase-messaging-sw.js] Received background message ', payload);
 // Customize notification here
 var notificationTitle = 'Background Message Title';
 var notificationOptions = {
 body: 'Background Message body.',
 icon: '/firebase-logo.png'
 };
 
 return self.registration.showNotification(notificationTitle,
 notificationOptions);
 });
 */
self.addEventListener('notificationclose', function (e) {
    var notification = e.notification;
    var primaryKey = notification.data.primaryKey;

    console.log('Closed notification: ' + primaryKey);
});

self.addEventListener('notificationclick', function (e) {
    var notification = e.notification;
    //var primaryKey = notification.data.primaryKey;
    var action = e.action;

    if (action === 'close') {
        notification.close();
    } else {
        clients.openWindow(notification.data.link);
        notification.close();
    }
});

self.addEventListener('push', function (event) {
    var data = event.data.json().notification;
    var options = {
        body: data.body,
        icon: data.icon,
        vibrate: [100, 50, 100],
        data: {
            //dateOfArrival: Date.now(),
            //primaryKey: '2',
            link: data.click_action
        },
        /*actions: [
         {action: 'explore', title: data.title,
         icon: 'images/checkmark.png'},
         {action: 'close', title: 'Close',
         icon: 'images/xmark.png'},
         ]*/
    };
    event.waitUntil(
		self.registration.showNotification(data.title, options)
	);
});