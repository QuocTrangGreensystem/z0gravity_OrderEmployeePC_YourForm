<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Project management software | YourPMStrategy.COM. YourPMStrategy.COM is an enterprise project management system that has all tools for managing projects online.">
        <meta name="keywords" content="PM, Project Management, Project Visum, project,management,software,collaborative,online,to-do list,planning,schedule,wiki pages,share documents">
        <meta name="author" content="Global SI - Green System Solutions">
        <meta name="language" content="US, FR">
        <meta charset="UTF-8">
		<meta name="robots" content="noindex">
		<meta name="robots" content="nofollow">
        <title><?php __("Project management software | YourPMStrategy.COM :: PROJECT ") ?></title>
	<link rel="stylesheet" href="css/common.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="/css/fonts.css" type="text/css" media="screen" />
	<!--[if IE]><link href="css/tooltip-ie-fix.css" rel="stylesheet" type="text/css"><![endif]-->
	<link rel="stylesheet" href="css/jquery-ui-1.8.4.custom.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/ColReorder.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/ColVis.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="css/demo_table.css" type="text/css" media="screen" />
</head>
<body>
<div id="wd-container-header">
	<div class="wd-layout">
		<h1 class="wd-logo"><a href="#"><?php __('Project manager')?></a></h1>
		<div class="wd-login">
			<ul>
				<li><div class="wd-image wd-photo"><a href="#"><img src="img/front/no-photo-small.png" alt="photo" /></a></div><div class="wd-name"><a href="#" class="wd-user">Phuc Le Bao</a></div></li>
				<li><a href="#" class="wd-sign-out"><?php __('Logout')?></a></li>
			</ul>
		</div>
	</div>
</div>
<div id="wd-container-main" class="wd-project-detail">
	<div class="wd-layout">
		<div id="wd-top-nav">
			<ul>
				<li><a href="index.html">Home</a></li>
				<li class="wd-current"><a href="project.html">Projects</a><span class="wd-current">arrow</span></li>
				<li><a href="#">Employees</a></li>
				<li class="wd-none"><a href="#">Administration</a></li>
			</ul>
		</div>
		<div class="wd-main-content">
			<div class="wd-title">
				<h2 class="wd-t1">Projects Listing</h2>
				<a href="#" class="wd-add-project">Add Project</a>				
				<ul class="wd-breadcrumb">
					<li><a class="wd-fist-link" href="#">Projects</a></li>
					<li><a href="#">Project Details</a></li>
				</ul>
			</div>
			<div class="wd-tab">
				<ul class="wd-item">
					<li class="wd-current"><a href="#wd-fragment-1">Project details</a></li>
					<li><a href="#wd-fragment-2">Project team </a></li>
					<li><a href="#wd-fragment-1">Phase planning</a></li>
					<li><a href="#wd-fragment-2">Milestones</a></li>
					<li><a href="#wd-fragment-1">Tasks</a></li>
					<li><a href="#wd-fragment-2">Risks</a></li>
					<li><a href="#wd-fragment-1">Issues</a></li>
					<li><a href="#wd-fragment-2">Decisions</a></li>
					<li><a href="#wd-fragment-2">Deliverable</a></li>					
				</ul>
				<div class="wd-panel">
					<div class="wd-section" id="wd-fragment-1">
						<h2 class="wd-t2">Project details</h2>
						 <!-- message -->
                            <div class="message success">
                                Success
                                <a href="#" class="close">x</a>
                            </div>
                            <!--div class="message error">
                                Error
                                <a href="#" class="close">x</a>
                            </div>
                            <div class="message">
                                Information
                                <a href="#" class="close">x</a>
                            </div>
                            <div class="message warning">
                                Warning
                                <a href="#" class="close">x</a>
                            </div-->
                        <!-- message.end -->
						<form method="post" action="">
							<fieldset>
								<div class="wd-scroll-form">
									<div class="wd-input wd-none wd-strong">
										<label for="project-name">Project Name</label>
										<input name="project-name" type="text" id="project-name"/>
									</div>	
									<div class="wd-left-content">
										<div class="wd-input">
											<label for="project-manager">Project Manager</label>
											<select id="project-manager" name="project-manager">
												<option>Mr Dungnh</option>
												<option>Mr Tuanpn</option>
											</select>
										</div>	
										<div class="wd-input">
											<label for="current-phase">Current Phase</label>
											<select id="current-phase" name="current-phase">
												<option>Mr Dungnh</option>
												<option>Mr Tuanpn</option>
											</select>
										</div>
										<div class="wd-input">
											<label for="priority">Priority</label>
											<select id="priority" name="priority">
												<option>Mr Dungnh</option>
												<option>Mr Tuanpn</option>
											</select>
										</div>								
										<div class="wd-input">
											<label for="status">Status</label>
											<select id="status" name="status">
												<option>Mr Dungnh</option>
												<option>Mr Tuanpn</option>
											</select>
										</div>
									</div>
									<div class="wd-right-content">
										<div class="wd-input wd-input-80">
											<label for="project-budget">Budget</label>
											<input name="project-budget" type="text" id="project-budget"/>
											<select id="project-budget-sel" name="project-budget-sel">
												<option>$ (USD)</option>
												<option>$ (CAD)</option>
											</select>
										</div>
										<div class="wd-input wd-calendar">
											<label for="startdate">Start Date</label>
											<input id="startdate" name="startdate" type="text"  class="wd-form-error"/>
											<div class="wd-error-message">error</div>
										</div>
										<div class="wd-input wd-calendar">
											<label for="plannedenddate">Planned End Date</label>
											<input id="plannedenddate" name="plannedenddate" type="text" />
										</div>
										<div class="wd-input wd-calendar">
											<label for="enddate">End Date</label>
											<input id="enddate" name="enddate" type="text" />
										</div>
									</div>								
									<div class="wd-input wd-area wd-none">
										<label for="project-objectives">Project Objectives</label>
										<textarea cols="50" rows="4" id="project-objectives" name="project-objectives"></textarea>
									</div>
									<div class="wd-input wd-area wd-none">
										<label for="constraint">Constraint</label>
										<textarea cols="50" rows="4" id="constraint" name="constraint"></textarea>
									</div>
									<div class="wd-input wd-area wd-none">
										<label for="remark">Remark</label>
										<textarea cols="50" rows="4" id="remark" name="remark"></textarea>
									</div>
								</div>
								<div class="wd-submit">
									<input type="submit" value="" class="wd-save"/>
									<a href="#" class="wd-reset">Reset</a>
								</div>
							</fieldset>
						</form>
					</div>					
				</div>
			</div>
		</div>
	</div>	
</div>	
<div id="wd-container-footer">
	<div class="wd-layout">
		<p class="wd-copy">Copyright &copy; 2012-2013. All rights reserved.</p>
	</div>
</div>
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/ColReorder.js"></script>
<script type="text/javascript" src="js/Scroller.js"></script>
<script type="text/javascript" src="js/ColVis.js"></script>
<!--[if IE]><script src="js/excanvas.compiled.js" type="text/javascript" charset="utf-8"></script><![endif]-->
<script src="js/jquery.bt.js" type="text/javascript"></script>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/jquery.ui.core.js" type="text/javascript"></script>	
<script src="js/jquery.ui.datepicker.js" type="text/javascript"></script>
<script type="text/javascript" src="js/common.js"></script>
</body>
</html>