# login-facebook-manually

1. Change the necessary information in fblogin.php and callback.php

2. Determine your domain and subpath (if it is existed)

3. Config your app's settings, match the Website Platform's site URL with your domain. Ex: http://domain.name

4. Active Client OAuth Login in Advanced, and set the url of callback.php. Ex: http://domain.name/callback.php

5. Open the http://domain.name/fblogin.php with the in-app browser. Make sure your can control your in-app browser to get accesstoken from the url.

6. Done.

How to use login facebook manually with inAppBrowser plugin in cordova

function login(){

  var ref = window.open('http://domain.name/fblogin.php', '_blank', 'location=no,closebuttoncaption=Done');
  
  var eventName = 'loadstart';
  
  ref.addEventListener(eventName, function(event) { 
  
  	var b = event.url.indexOf("?token=");
  	
  	console.log ('eee', event.url, b);
  	
    if(b !== -1)
    {
        var token = event.url.match(/token=(.*)$/)[1];
        
        console.log('token:' +  token);
        
        token = token.replace ('#_=_', "");
        
        localStorage.setItem("token", token);
        
        // call your callback here
        
        ref.close();
   }
   
  });
  
}

function getUserInfo(){

  var scopeValue = "fields=id,email,picture,name, first_name, last_name, gender, birthday, bio,quotes, interests, likes, location, work";
  
  var graphAPIUrl = 	"https://graph.facebook.com/me?" + scopeValue + "&access_token=" +  localStorage.getItem("token");
  
  $.getJSON(graphAPIUrl, function(data){
  
        localStorage.setItem("userId", data.userId);
        
				console.log("getting data", JSON.stringifyda);
				
				if (data.error) {
				
					alert("error : " + data.error);
					
				} else {
				
				 	// console.log(data);
				 	
				}
				
  });
  
}

function feedWall(options){

  var token = localStorage.getItem("token");
  
	var userID = localStorage["fbID"];
	
	$.ajax({
	
      type: 'post',
      
      url: "https://graph.facebook.com/"+userID+"/feed",
      
      data: {
          "access_token": token,
          
          "message" : message,
          
          "link": options.urlLink,
          
          "picture": options.urlPicture,
          
          "name": options.name,
          
          "description": options.description
      },
      
      success: function(data) {
      
          //alert ('oo:' + JSON.stringify(data));
          
          console.log ('postFBWall success:' + JSON.stringify(data));
          
          if (data.error){
          
          	navigator.notification.alert("You deny the publich_actions permission. AlphaTrainer could not share information on your wall");
          }
      },
      
      error: function(xhr, status, error) {
      
          //alert ('oo:' + JSON.stringify(arguments));
          
          console.log  ('postFBWall error:' + JSON.stringify(arguments));
      }
      
  });
  
}
