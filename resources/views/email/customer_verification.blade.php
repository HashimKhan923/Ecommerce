<!-- <h1>Hello {{$name}}</h1>
<p>
    Token: {{ $token }}
</p> -->

<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Dragon Auto Mart</a>
    </div>
    <p style="font-size:1.1em">Hi, Mr. {{$name}}</p>

    <p>Welcome to Dragon Auto Mart! We're excited to have you on board for this incredible journey. If you ever have any questions, concerns, or suggestions</p> 
    
    <p>don't hesitate to contact us at support@dragonautomart.com.</p>


    <p>click on the following Link to complete your email verification procedures.</p>
    <h5><a href="https://api.dragonautomart.com/api/verification/{{$token}}">https://dragonautomart.com/</a></h5>
    <p style="font-size:0.9em;">Regards,<br />Dragon Auto Mart</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <!-- <p>Your Brand Inc</p>
      <p>1600 Amphitheatre Parkway</p>
      <p>California</p> -->
    </div>
  </div>
</div>