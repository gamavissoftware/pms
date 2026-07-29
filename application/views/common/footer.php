
<!--<script>
$(window).on("blur focus", function(e) {
var prevType = $(this).data("prevType");

    if (prevType != e.type) {   
        switch (e.type) {
            case "blur":
                setTimeout( function(){ 
				
    location.href="https://prestomitr.com/User/signout";
  }  ,60000000);
                break;
            case "focus":
               
                break;
        }
    }

    $(this).data("prevType", e.type);
})
</script>-->

<footer class="footer text-right">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 text-center">
                                 <?php echo date('Y');?> © GAMAVIS SOFTECH LLP
								 
								<script type="text/javascript"> //<![CDATA[
  var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
  document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
  TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png", "POSDV", "none");
</script>
                            </div>
                            
                        </div>
                    </div>
                </footer>

                <?php $this->load->view('task_management/_notification_poller'); ?>

                <script>
function myFunction() {
  var x = document.getElementById("mobilemenuwrap");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}
</script>
