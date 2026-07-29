<!DOCTYPE html>


<html>


    <head>


        <meta charset="utf-8">


        <meta name="viewport" content="width=device-width, initial-scale=1.0">


        <meta name="description" content="">


        <meta name="author" content="<?php echo copyright; ?>">





        <link rel="shortcut icon" href="<?php echo assets_url;?>images/favicon.ico">





        <title><?php echo sitetitle; ?>Supplier Registration Form</title>





        <!-- Table Responsive css -->


		<script src="<?php echo assets_url;?>js/angular.min.js"></script>


		 <!-- DataTables -->


        <link href="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/fixedHeader.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>plugins/datatables/scroller.bootstrap.min.css" rel="stylesheet" type="text/css" />


		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


		<script type="text/javascript" src="<?php echo assets_url;?>ckeditor/ckeditor.js"></script> 


        <link href="<?php echo assets_url;?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/core.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/components.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/icons.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/pages.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/menu.css" rel="stylesheet" type="text/css" />


        <link href="<?php echo assets_url;?>css/responsive.css" rel="stylesheet" type="text/css" />


		<link href="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


		<link href="assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">


        <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->


        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->


        <!--[if lt IE 9]>


        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>


        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>


        <![endif]-->





        <script src="<?php echo assets_url;?>js/modernizr.min.js"></script>
	<?PHP 
$q = $this->db->select('company_name, logo, colorcode')->from('company_information')->get();
foreach($q->result() as $LOGO);
?>

    <style>


table.manglesh thead th {


				background: red;


				color:#fff;


				font-weight:bold;


			}


			


#pageloader


{


  background: rgba( 255, 255, 255, 0.8 );


  display: none;


  height: 100%;


  position: fixed;


  width: 100%;


  z-index: 9999;


}


#pageloader img


{


  left: 50%;


  margin-left: -32px;


  margin-top: -32px;


  position: absolute;


  top: 50%;


}


.supp {
            border: 1px dotted black;
            padding: 20px;
            margin-top: 50px;
            margin-bottom: 50px;
            height: auto;
        }
        
        .sel {
            width: 100%;
            border: none;
            border-bottom: 1px solid lightgray;
            outline: none;
            margin-top: 26px;
        }
        
        .inp {
            border: none;
            border-bottom: 1px solid lightgray;
            padding: 5px 0px;
            outline: none;
            width: 100%;
            color: black;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        
        [placeholder]:focus::-webkit-input-placeholder {
            transition: text-indent 0.4s 0.4s ease;
            text-indent: -100%;
            opacity: 1;
        }
        
        .supp label {
            margin: 0;
        }
        
        .supp p {
            font-size: 9px;
            margin: 0;
            margin-top: 2px;
        }
        
        .supp h4 {
            font-size: 20px;
        }
        
        .form-check {
            margin-top: 10px;
        }


</style>


	</head>








    <body>








        <!-- Navigation Bar-->


                <header id="topnav">


          <?php $this->load->view('common/nav-menu');?>


        </header>


        <!-- End Navigation Bar-->


<div class="wrapper">
    <div class="container-fluid" >
<div class="dashboard-header">
<h1>Supplier Registration Form</h1><hr>
</div>

<div class="container">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 supp">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-sm-12">
                            <form>
                                <div class="form-group">
                                    <label>Short Listed Product Range<span style="color: red;">*</span></label>

                                    <div style="margin-top: 20px; margin-bottom: 20px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                            INJECTION MOULDS
                                    </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                            RUBBER MOULDS
                                </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                            RUBBER INJECTION MOULDS
                                </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                            PRESSURE DIE CASTINGS
                                </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                            <label class="form-check-label" for="flexCheckChecked">
                                            MOULDING & PART PRODUCTION
                                </label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Company Name<span style="color: red;">*</span></label>
                                        <p>Company Official Name</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>




                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Supplier Location<span style="color: red;">*</span></label>
                                            <p>Company Official Name</p>
                                            <input class="inp" placeholder="Your Answer" required="" />
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label>Company Website<span style="color: red;">*</span></label>
                                        <p>Company Official Website (Need to check with its details given such as name address directors names etc.</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>




                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>Company Profile<span style="color: red;">*</span></label>
                                            <p>A Detailed Company profile including Infrastructure, Company Vision/ Mission & Goals</p>
                                            <input type="file" id="customFile" / style="margin-top: 10px; margin-bottom: 10px;" required="">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label>Products Catalogue <span style="color: red;">*</span></label>
                                        <p>Must include the entire range of the products</p>
                                        <input type="file" id="customFile" / style="margin-top: 10px; margin-bottom: 10px;" required="">
                                    </div>

                                    <div class="form-group">
                                        <label>Upload Company OPL Report</label>
                                        <p>Get the Company OPL from Bank via verification (Paid Service and upload the Certificate)</p>
                                        <input type="file" id="customFile" / style="margin-top: 10px; margin-bottom: 10px;">
                                    </div>


                                    <div class="form-group">
                                        <label>Upload Company Business License <span style="color: red;">*</span></label>
                                        <p>Get the Company OPL from Bank via verification (Paid Service and upload the Certificate)</p>
                                        <input type="file" id="customFile" / style="margin-top: 10px; margin-bottom: 10px;" required="">
                                    </div>


                                    <div class="form-group">
                                        <label>Company Registration ID / No.<span style="color: red;">*</span></label>
                                        <p>The new 18-digit business registration number actually goes by the Chinese name “统一社会信用代码” which could be translated as “Unified Social Credit Code”. But for ease of understanding, and given that it is displayed
                                            on a company’s business license, we simply refer to it as the “Chinese business registration number”.</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>



                                    <div class="form-group">
                                        <label>Company Type<span style="color: red;">*</span></label>
                                        <!--------------------------->
                                        <div style="margin-top: 20px; margin-bottom: 20px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                        Limited Liability Company (invested by foreign invested company)
                                    </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                Limited Liability Company (invested by foreign investor)
                                    </label>
                                            </div>


                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                Limited Liability Company (invested by Hong Kong/Taiwan Macau investors)
                                    </label>
                                            </div>


                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                Limited Liability Company (invested by companies)
                                    </label>
                                            </div>


                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                PRESSURE DIE CASTINGS
                                    </label>
                                            </div>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                Limited Liability Company (invested by Natural Person)
                                    </label>
                                            </div>

                                        </div>


                                        <!--------------------------------->
                                    </div>


                                    <div class="form-group">
                                        <label>Company Registered Address<span style="color: red;">*</span></label>
                                        <p>Registered address mention on the Business License</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <h4>Legal Representative</h4>
                                    <p>This is directly related to Company Owners / Director</p>
                                    <hr>


                                    <div class="form-group">
                                        <label>Company Legal Representative Name<span style="color: red;">*</span></label>
                                        <p>The China company legal representative can feasibly be anyone from the company. They have been chosen to have their name put on the business license and have agreed to this arrangement.</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <div class="form-group">
                                        <label>Company Legal Representative Mobile Number<span style="color: red;">*</span></label>

                                        <input name="form_number" class="inp" value="" required="" placeholder="Your Answer">
                                    </div>

                                    <div class="form-group">
                                        <label>Company Legal Representative Email ID <span style="color: red;">*</span></label>

                                        <input name="form_email" class="inp" value="" type="email" required="" placeholder="Your Answer">
                                    </div>


                                    <div class="form-group">
                                        <label>Company Legal Representative WeChat ID<span style="color: red;">*</span></label>

                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <h4>Company Spokes Person</h4>
                                    <p>This is directly related to the Sales person or EA of the Director who is dealing with on/behalf of the Company</p>
                                    <hr>

                                    <div class="form-group">
                                        <label>Company Spokes Person Name<span style="color: red;">*</span></label>
                                        <p>Name the Sales person or EA of the Director who is dealing with on/behalf of the Company</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Company Spokes Person Mobile<span style="color: red;">*</span></label>
                                        <p>Mobile number the Sales person or EA of the Director who is dealing with on/behalf of the Company</p>
                                        <input name="form_number" class="inp" value="" required="" placeholder="Your Answer">
                                    </div>

                                    <div class="form-group">
                                        <label>Company Spokes Person Email ID<span style="color: red;">*</span></label>
                                        <p>Official Email ID of the Sales person or EA of the Director who is dealing with on/behalf of the Company</p>
                                        <input name="form_email" class="inp" value="" type="email" required="" placeholder="Your Answer">
                                    </div>

                                    <div class="form-group">
                                        <label>Company Spokes Person WeChat <span style="color: red;">*</span></label>
                                        <p>WeChat ID of the Sales person or EA of the Director who is dealing with on/behalf of the Company</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <div class="form-group">
                                        <label>Company Registered Capital Amount in RMB <span style="color: red;">*</span></label>
                                        <p>One of the basic checks you may make about a Chinese company is to look up how much capital it was registered with. Proving a substantial amount of allocated capital is a requirement for registering a business in
                                            China, with the aim of ensuring that the company will be adequately funded and able to operate as described.</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Date of Establishment<span style="color: red;">*</span></label>
                                        <p>Finding out when and where a Chinese company registered is a basic step in checking that they’re a legitimate company you want to do business with. This simple check can be a very easy way to raise red flags in
                                            situations such as the following: The company is not registered in the city or province they claim to be The company has not been registered for as long as they claim to have been operating The company’s date
                                            of registration raises doubts about their level of development</p>
                                        <input class="inp" placeholder="Your Answer" type="date" required="" />
                                    </div>


                                    <div class="form-group">
                                        <label>License Expiry Date<span style="color: red;">*</span></label>

                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <div class="form-group">
                                        <label>Business Scope<span style="color: red;">*</span></label>
                                        <p>What kind of business we can do with this supplier. In what all kinds of products and services, this company deals in</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Registration Bureau<span style="color: red;">*</span></label>
                                        <p>Through which Registration Bureau, the company got registration</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <h4>UPLOAD BANK DETAILS</h4>
                                    <p>Enter the Complete Information of the Bank, through which the company will received payments</p>
                                    <hr>

                                    <div class="form-group">
                                        <label>Beneficiary Name <span style="color: red;">*</span></label>
                                        <p>Name of the Account in which the amount will reflect</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Beneficiary Address<span style="color: red;">*</span></label>
                                        <p>Registered Address of the company in the Bank</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Beneficiary Account No.</label>
                                        <p>Please write your bank account number</p>
                                        <input class="inp" placeholder="Your Answer" />
                                    </div>


                                    <div class="form-group">
                                        <label>Beneficiary Bank Name <span style="color: red;">*</span></label>
                                        <p>Name of the Bank in which Amount will reflect</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Beneficiary Bank Address<span style="color: red;">*</span></label>
                                        <p>Address of the Bank (Branch) in which Amount will reflect</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Beneficiary Swift Code <span style="color: red;">*</span></label>
                                        <p>Swift Code of the Beneficiary Account</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Beneficiary Bank Code <span style="color: red;">*</span></label>

                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Intermediary Bank Name<span style="color: red;">*</span></label>
                                        <p>Please write Bank Name</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Intermediary Bank Address<span style="color: red;">*</span></label>
                                        <p>Please write bank address</p>
                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>

                                    <div class="form-group">
                                        <label>Intermediary Bank Swift Code<span style="color: red;">*</span></label>

                                        <input class="inp" placeholder="Your Answer" required="" />
                                    </div>


                                    <input class="btn btn-primary" type="submit" value="Submit">


                            </form>



                            </div>

                        </div>
                    </div>

                </div>
                <div class="col-sm-2"></div>
            </div>
        </div>

  </div>
  </div>
    













                <!-- Footer -->


               <?php $this->load->view('common/footer');?>


                <!-- End Footer -->





            </div> <!-- end container -->


        </div>


        <!-- end wrapper -->








                <!-- jQuery  -->


        <script src="<?php echo assets_url;?>js/jquery.min.js"></script>


        <script src="<?php echo assets_url;?>js/bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>js/detect.js"></script>


        <script src="<?php echo assets_url;?>js/fastclick.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.slimscroll.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.blockUI.js"></script>


        <script src="<?php echo assets_url;?>js/waves.js"></script>


        <script src="<?php echo assets_url;?>js/wow.min.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.nicescroll.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.scrollTo.min.js"></script>





        <!-- Datatables-->


        <script src="<?php echo assets_url;?>plugins/datatables/jquery.dataTables.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.bootstrap.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.buttons.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/jszip.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/pdfmake.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/vfs_fonts.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.html5.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/buttons.print.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.fixedHeader.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.keyTable.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.responsive.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/responsive.bootstrap.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/datatables/dataTables.scroller.min.js"></script>


        <script src="<?php echo assets_url;?>plugins/timepicker/bootstrap-timepicker.min.js"></script>


        <!-- Datatable init js -->


        <script src="<?php echo assets_url;?>pages/datatables.init.js"></script>


<script src="<?php echo assets_url;?>plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>


        <!-- App js -->


        <script src="<?php echo assets_url;?>js/jquery.core.js"></script>


        <script src="<?php echo assets_url;?>js/jquery.app.js"></script>


		


 


</body>


</html>