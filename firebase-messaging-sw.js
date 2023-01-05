importScripts('https://www.gstatic.com/firebasejs/7.22.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/7.22.1/firebase-messaging.js');
/*Update this config*/
 var config = {
       apiKey: "AIzaSyCzx3ZWmArchg5WqmdLgWGrlMyGertwkHc",
  authDomain: "iltproduct.firebaseapp.com",
  projectId: "iltproduct",
  storageBucket: "iltproduct.appspot.com",
  messagingSenderId: "745435388006",
  appId: "1:745435388006:web:9463349acb5eb6cd1235a7",
  measurementId: "G-8HB69L3CR1"
      };
  firebase.initializeApp(config);

const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = payload.data.title;
  const notificationOptions = {
    body: payload.data.body
  };

  return self.registration.showNotification(notificationTitle,
      notificationOptions);
});
// [END background_handler]
