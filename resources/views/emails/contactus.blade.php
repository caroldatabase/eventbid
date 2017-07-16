<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Contact Us</title>
<style type="text/css">
table {
    border-collapse: collapse;
    border-color:#ccc;
     font-family:Arial, Helvetica, sans-serif ;
}
</style>
</head>
<body>
 
<table width="600" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#971800" style="background-color:#fff;">
      <tr>
          <td align="center" valign="top" bgcolor="#ffffff" >
            <table width="90%" border="0" cellspacing="0" cellpadding="10" style="margin-bottom:10px;">
              <tr>
                <td align="left" valign="top" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#000;"> 
                  <div>
                        <p>Hello Eventbid Team,</p>
                        <p> </p>
                        <p>You have new contact mail.
                        </p>
                        <table border="1"> 
                        @foreach($content as $key => $value)
                          <tr>
                            <td>{{ ucfirst($key)}}</td>
                            <td>{{$value}}</td>
                          </tr>
                          @endforeach

                        </table>
                          <p>Best Regards,</p>
                          <p>{{$content['firstName']}}</p> 

                  </div>
                </td>
            </tr>
          </table>
        </td>
      </tr>
  </table>
</body>
</html>
