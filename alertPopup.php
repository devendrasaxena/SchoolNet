
<div class="modal fade dashboard" id="common-msg" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="msg" id="common-msg-text"></div>

            </div>
            <div class="modal-footer">
                 <a href="javascript(0)" class="" data-dismiss="modal"  id="ok">
				 <div  class="col-sm-12 text-center cancelRnone">  
                   OK
                </div></a>

            </div>

        </div>
    </div>
</div>

<div class="modal fade dashboard" id="reg-popup" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="msg"></div>

            </div>
            <div class="modal-footer">
                 <a href="javascript(0)" data-dismiss="modal" status="0" class="ok">
				  <div  class="col-sm-12 text-center cancelRnone">  
                  OK
                </div></a>

            </div>

        </div>
    </div>
</div>


<div class="modal fade dashboard" id="profile-pic-removal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="msg"></div>

            </div>
            <div class="modal-footer">
                  <a href="javascript(0)" class="col-sm-6" data-dismiss="modal" c_edge_id="" id="remove-profile-pic-confirm">
				  <div  class="col-sm-12 text-center cancelRnone" >  
                  Remove
                </div></a>

                 <a href="javascript(0)" class="col-sm-6" data-dismiss="modal" >
				 <div  class="col-sm-12 text-center cancelRnone">  
                   Cancel
                </div></a>

            </div>

        </div>
    </div>
</div>


<div class="modal fade dashboard" id="no-word-found" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="msg"></div>

            </div>
            <div class="modal-footer">
                <a href="javascript(0)" class="ok" data-dismiss="modal" >
				 <div  class="col-sm-12 text-center cancelRnone">  
                   OK
                </div></a>

            </div>

        </div>
    </div>
</div>

<div class="modal fade dashboard" id="confirmationModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">

            </div>
            <div class="modal-body">
                <div class="msg"></div>

            </div>
            <div class="modal-footer">
			<a href="javascript(0)" class="cancel" data-dismiss="modal" id="btnNo">
				 <div  class="col-sm-12 text-center cancelRnone">  
                   Cancel
                </div></a>

                <a href="javascript(0)" class="ok" id="btnYes">
				 <div  class="col-sm-12 text-center cancelRnone">  
                   OK
                </div></a>

            </div>

        </div>
    </div>
</div>


<style>
#latest_notifications .dropdown-item{
   border-bottom: 1px solid lightblue;
    padding: 10px 5px;
}
#latest_notifications .dropdown-item .date{
  font-size: 11px;
}
#latest_notifications .dropdown-item p{
  margin-bottom: 5px;
}
</style>
<script>

  const server_url = 'https://wfpstaging.englishedge.in/ilt/notification_api/public/api';

  const user_id = <?=isset($_SESSION['user_id'])?$_SESSION['user_id']:0?>

</script>



<!--  <div class="alert alert-info sizePopup"  id="infoNotification" style="display: none;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <span class="msg"></span>
</div>-->

<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script src="https://www.gstatic.com/firebasejs/7.22.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.22.1/firebase-messaging.js"></script>
<script src="https://wfpstaging.englishedge.in/new/notification_ui/js/firebase_script.js"></script

<script>
// Initialize the Firebase app by passing in the messagingSenderId
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

navigator.serviceWorker.register('/firebase-messaging-sw.js')
.then(function (registration) {
    messaging.useServiceWorker(registration);
        
    // Request for permission
    messaging.requestPermission()
    .then(function() {
      //console.log('Notification permission granted.');
      //console.log('user_id', user_id);
      // TODO(developer): Retrieve an Instance ID token for use with FCM.
      messaging.getToken()
      .then(function(currentToken) {
        if (currentToken && user_id) {
          //console.log('Token: ' + currentToken)
          //console.log('user: ' + user_id)
          sendTokenToServer(currentToken, user_id);
        } else {
          console.log('No Instance ID token available. Request permission to generate one.');
          setTokenSentToServer(false);
        }
      })
      .catch(function(err) {
        console.log('An error occurred while retrieving token. ', err);
        setTokenSentToServer(false);
      });
    })
    .catch(function(err) {
      console.log('Unable to get permission to notify.', err);
    });
});

// Handle incoming messages
messaging.onMessage(function(payload) {
  //console.log("Notification received: ", payload);
   
  if(getUrlVars()['user_id'] === payload.data.user_id){
    updateClient(payload.data.notification_id);
    toastr["info"](payload.data.body, payload.data.title);
  }
  
  
});


// Callback fired if Instance ID token is updated.
messaging.onTokenRefresh(function() {
  messaging.getToken()
  .then(function(refreshedToken) {
    console.log('Token refreshed.');
    // Indicate that the new Instance ID token has not yet been sent 
    // to the app server.
    setTokenSentToServer(false);
    var user_id = getUrlVars()['user_id'];

    // Send Instance ID token to app server.
    sendTokenToServer(refreshedToken, user_id);
  })
  .catch(function(err) {
    console.log('Unable to retrieve refreshed token ', err);
  });
});

//get user_id from url
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,    
    function(m,key,value) {
      vars[key] = value;
    });
    return vars;
  }

// Send the Instance ID token your application server, so that it can:
// - send messages back to this app
// - subscribe/unsubscribe the token from topics
function sendTokenToServer(currentToken, user_id) {
  if (!isTokenSentToServer()) {
    console.log('Sending token to server...');
    // TODO(developer): Send the current token to your server.
    if(saveToken(currentToken, user_id)){
      setTokenSentToServer(true);
    }

    if (!isTokenSentToServer()) {
      console.log('saved');
    }else{
      console.log('Please try again to save token');
    }
    
  } else {
    console.log('Token already sent to server so won\'t send it again ' +
        'unless it changes');
  }
}

function isTokenSentToServer() {
  return window.localStorage.getItem('sentToServer') == 1;
}

function setTokenSentToServer(sent) {
  window.localStorage.setItem('sentToServer', sent ? 1 : 0);
}


 function saveToken(currentToken, user_id) {
 // alert("USER ID::::"+user_id+"-TOKEN :::"+currentToken);
  if(user_id === '' || user_id === null || user_id === undefined){
    return false;
  }
     
	  $.ajax({
        url: server_url+'/save_user',
        method: 'post',
        data: {'msg_token' : currentToken, 'user_id':user_id},
      }).done(function(result){
        if(result.success){
          window.localStorage.setItem('sentToServer', 1);
        }else{
          window.localStorage.setItem('sentToServer', 0);
        }
      })
    }

function updateClient(notification_id, user_id){
  $.ajax({

        url: server_url+'/update_notification_status',
        method: 'post',
        data: {'status' : 'received', 'user_id' :user_id, 'notification_id' : [notification_id]},
      }).done(function(result){
        //console.log(result);
      })
}


</script>




 <script> 
	$(document).ready(function(){
	  $('#lang').change(function(){
		// Call submit() method on <form id='myform'>
		$('#myform').submit();
	  });
	});


function notfication_counter(user_id){

	$.post(server_url+'/getPendingNotifications', {'user_id': user_id}, function(data){
		//console.log(data);

var notifications_html = [];
if(data.success && data.data.length > 0){
var notifications = data.data;

var received_msgs = [];

$.each(notifications, function(i,v){

 //display the pending notifications in pop up as well
 if(v.status === 'pending'){
  toastr["info"](v.message);
  received_msgs.push(v._id);
 }
 
})

if(received_msgs.length > 0){
  //update the messages received.
  $.ajax({
	  url: server_url+'/update_notification_status',
	  method: 'post',
	  data: {'status' : 'received', 'user_id' :user_id, 'notification_id' : received_msgs},
	}).done(function(result){
	  //console.log(result);
	})
}


}

$('.badge').html(data.pending_notifications);

})

}


notfication_counter(user_id);


$('.notiDiv').on('click', function(e){
	e.stopPropagation();
    
	  if ($("#profileDrop").hasClass("show")) {
		$("#rightArrowMenu").click(); 
		  $(".noti-menu").toggle();		
	  }else{
		  $(".noti-menu").toggle();  
	  }
    get_pending_notifications(user_id);
});
$('#rightArrowMenu').on('click', function(e){
	$(".noti-menu").hide();
});
$("body").click(function(){
  $(".noti-menu").hide();
});


function get_pending_notifications(user_id){
	
	  
    $.post(server_url+'/getPendingNotifications', {'user_id': user_id, 'status': 'received'}, function(data){
  

          var notifications_html = [];
        if(data.data.length > 0){
          var notifications = data.data;

          $('#total_pending').html(data.data.length);

          $.each(notifications, function(i,v){

            let creation_time = v.creation_time;
            var date = '';
            var time = '';
            if(creation_time !=='' && creation_time !== null && creation_time !== undefined){
              date = creation_time.split(" ")[0];
              time = creation_time.split(" ")[1];
            }else{
              let updated_at = new Date(v.updated_at);
              var d = updated_at.getDate() < 10 ? '0'+updated_at.getDate() : updated_at.getDate();
              var m = updated_at.getMonth() < 10 ? '0'+updated_at.getMonth() : updated_at.getMonth();
              date = d+'/'+m+'/'+updated_at.getFullYear();
              var hours = updated_at.getHours();
              var ampm = hours >= 12 ? 'PM' : 'AM';
              var gethours = hours % 12;
              var  hour = gethours < 10 ? '0'+gethours : gethours;
              var  minutes = updated_at.getMinutes() < 10 ? '0'+updated_at.getMinutes() : updated_at.getMinutes();
              time = hour+':'+minutes+ampm;

            }

            notifications_html.push('<a href="notification.php?id='+v._id+'"><li><div class="dropdown-item view_msg" href="notification.php" data-date="'+date+'" data-time="'+time+'" data-message="'+encodeURIComponent(v.message)+'" class="view_msg" data-id = '+v._id+'><p>'+v.message+'</p><div class="date"> <span> '+date+'</span> <span>'+time+'</span></div></div></li></a>')
          })
        }else{
            notifications_html.push('<li><div class="dropdown-item">No pending notification found.</div></li>')
         }
         $('#notification_counter').html('<span>'+data.pending_notifications+'</span>');
     $('.count').html('<span>'+data.pending_notifications+'</span>');
     $('.badge').html(data.pending_notifications);

     
          $('#latest_notifications').html(notifications_html);
          $('#bellN #paging').remove();
          if(data.pending_notifications > 10){
            $("#latest_notifications").JPaging({
              pageSize: 10
            });
          }
          
      })
  }
  

</script>-->

