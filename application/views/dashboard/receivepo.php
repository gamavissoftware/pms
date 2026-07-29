 <div id="receivenewpo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">

 				<form id="loginForm" method="post" action="<?php echo page_url;?>Task/directpaymenttermmaster"  enctype="multipart/form-data">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <div class="modal-header">

                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>

                                            <h4 class="modal-title">Add New Payment Term</h4>

                                        </div>

                                        <div class="modal-body">

                                            <div class="row">

                                                <div class="col-md-12">

                                                    <div class="form-group">
														<label for="field-1" class="control-label">Payment Term</label>
														<span id="error_paymentterms" style="color:red;">*</span>
														<input type="text" class="form-control" name="paymentterms" id="paymentterms" value="" required>
												    </div>

                                                </div>

                                                <div class="col-md-3">
                                                	<div class="form-group">
                                                		<label>Payment (%)</label>
                                                		<span style="color:red">*</span>
                                                		<input type="number" class="form-control" name="paymentpercentage[]" id="paymentpercentage" value="">
                                                		
                                                	</div>
                                                </div>

												<div class="col-md-8">
													<div class="form-group">
														<label>Milestone</label> 
														<span style="color: red;" id="error_milestone">*</span>
												<select class="form-control" id="milestone" name="milestone[]">
													<option value="">--Select Milestone--</option>
													<?php
													$businessloc = 2;
													 $query = $this->db->select('task_id, task_name')->from('task_management')->where('status','1')->get();
													foreach($query->result() as $task){?>
													<option value="<?php echo $task->task_id;?>"><?php echo $task->task_name;?></option>
													<?php }?>
												</select>
													</div>
												</div>
												
												
											
												<div class="col-md-1">
												<div class="form-group" style="padding-top:23px">
													<button type="button" class="btn btn-warning" name="add" id="addmore_btn1"><i class="fa fa-plus"></i></button>
												</div>
												
												</div>

												<div id="dynamictasks1"></div>
                                            </div>

                                        </div>

                                        <div class="modal-footer">

                                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>

                                            <input type="submit" id="depsave" class="btn btn-info" value="Submit"> 

                                        </div>

                                    </div>

                               

								</form>

                            </div>