<?php 
if(!isset($this->session->userdata['wallmart']['user_id']))
{

}

$user_id =$this->session->userdata['wallmart']['user_id'];
$profilepic="";
 ?>
<style>
 .badge1 {
		position:relative;
	}
	.badge1[data-badge]:after {
		content:attr(data-badge);
		position:absolute;
		top:-10px;
		right:-10px;
		font-size:12px;
		font-weight:bold;
		background:green;
		color:white;
		width:18px;height:18px;
		text-align:center;
		line-height:18px;
		border-radius:50%;
		box-shadow:0 0 1px #333;
	}
	.quotecss{
		color: #fff;
    text-align: center;
    padding-top: 26px;
    font-size: 13px;
		font-weight:bold;
	}
	#topnav .topbar-main {
  /*background-color: #2986CE;*/
  background-image: url('https://prestomitr.com/assets/images/pt.jpg');
  height: 56px;
}
.usernamecss {
    margin-top:22px;
  }
@media only screen and (max-width: 600px) {
  .usernamecss {
    margin-top:0px;
  }
}

.secondul
{
    max-height:450px;
    overflow-y:auto;
}
 </style>

 <div class="leftmenu" id="mobilemenu">
	<div class="logo" style="background-color: #394253;">
		<a href="#"><img src="<?php echo assets_url;?>images/wallmart.png" alt="Logo"></a>
	</div>
	<div id="mobilemenuwrap">
		<ul>
			<li>
				<a class="active" href="<?php echo page_url;?>Dashboard"><i class="fa fa-dashboard"></i> Dashboard</a>
			
			</li>



		</ul>
	</div>
	<a href="javascript:void(0);" class="icon colexpicon" onclick="myFunction()">
		<i class="fa fa-bars"></i>
	</a>
</div>

 <header id="topnav">
            <div class="topbar-main">
                <div class="container-fluid">

                   <div class="menu-extras">
                        
                      
                        
                        <ul class="nav navbar-nav navbar-right pull-right">
                           
                          <li class="dropdown user-box">
                               
                                <div >
                                    <ul class="list-inline m-b-0">
                                        <li>
                                             <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true" style="font-size: 30px;color: #fff;line-height: 56px;">
                                                <i class="fa fa-angle-down" aria-hidden="true"></i>
                                    <ul class="dropdown-menu">
                                
                                   
                                    <li><a href="<?php echo page_url;?>Walmart/signout"><i class="ti-power-off m-r-5"></i> Logout</a></li>
                                  
                                </ul>

                                            </a>
                                           
                                        </li>
                                    </ul>
                                </div>
                                
                            </li>
                            <li class="usernamecss"><span style="color:#fff;">Walmart</span></li>

                            <li class="dropdown user-box">
                                <a href="" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
                                   
                                     <img src="<?php echo page_url;?>image_bank/wallmartunit.png" alt="user-img" class="img-circle user-img"> 
                                    <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div>
                                </a>

                               
                            </li>
                        </ul>
                       
                    </div>

                </div>
            </div>

           
        </header>