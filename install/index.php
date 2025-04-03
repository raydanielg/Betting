<?php
    $itemName = 'betlab';
    error_reporting(0);
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    function appUrl() {
        $current = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $exp     = explode('?action', $current);
        $url     = str_replace('index.php', '', $exp[0]);
        $url     = substr($url, 0, -8);
        return $url;
    }
    function checkSecurePassword($password) {
        $passwordError = false;
        $capital       = "/[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/";
        $lower         = "/[abcdefghijklmnopqrstuvwxyz]/";
        $number        = "/[1234567890]/";
        $special       = '/[`!@$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?~]/';
        $hash          = '/[#]/';
        if (!preg_match($capital, $password)) {
            $passwordError = true;
        } else if (!preg_match($lower, $password)) {
            $passwordError = true;
        } else if (!preg_match($number, $password)) {
            $passwordError = true;
        } else if (!preg_match($special, $password)) {
            $passwordError = true;
        } else if (strlen($password) < 6) {
            $passwordError = true;
        } else if (preg_match($hash, $password)) {
            $passwordError = true;
        }
        if ($passwordError) {
            throw new Exception("Weak password detected.");
        }

    }
    if ($action == 'requirements') {
        $passed      = [];
        $failed      = [];
        $requiredPHP = 8.3;
        $currentPHP  = explode('.', PHP_VERSION)[0] . '.' . explode('.', PHP_VERSION)[1];
        if ($requiredPHP == $currentPHP) {
            $passed[] = "PHP version $requiredPHP is required";
        } else {
            $failed[] = "PHP version $requiredPHP is required. Your current PHP version is $currentPHP";
        }
        $extensions = ['BCMath', 'Ctype', 'cURL', 'DOM', 'Fileinfo', 'GD', 'JSON', 'Mbstring', 'OpenSSL', 'PCRE', 'PDO', 'pdo_mysql', 'Tokenizer', 'XML', 'Filter', 'Hash', 'Session', 'zip'];
        foreach ($extensions as $extension) {
            if (extension_loaded($extension)) {
                $passed[] = strtoupper($extension) . ' PHP Extension is required';
            } else {
                $failed[] = strtoupper($extension) . ' PHP Extension is required';
            }
        }
        if (function_exists('curl_version')) {
            $passed[] = 'Curl via PHP is required';
        } else {
            $failed[] = 'Curl via PHP is required';
        }
        if (file_get_contents(__FILE__)) {
            $passed[] = 'file_get_contents() is required';
        } else {
            $failed[] = 'file_get_contents() is required';
        }
        if (ini_get('allow_url_fopen')) {
            $passed[] = 'allow_url_fopen() is required';
        } else {
            $failed[] = 'allow_url_fopen() is required';
        }
        $dirs = ['../core/bootstrap/cache/', '../core/storage/', '../core/storage/app/', '../core/storage/framework/', '../core/storage/logs/'];
        foreach ($dirs as $dir) {
            $perm = substr(sprintf('%o', fileperms($dir)), -4);
            if ($perm >= '0775') {
                $passed[] = str_replace("../", "", $dir) . ' is required 0775 permission';
            } else {
                $failed[] = str_replace("../", "", $dir) . ' is required 0775 permission. Current Permisiion is ' . $perm;
            }
        }
        if (file_exists('database.sql')) {
            $passed[] = 'database.sql should be available';
        } else {
            $failed[] = 'database.sql should be available';
        }
        if (file_exists('../.htaccess')) {
            $passed[] = '".htaccess" should be available in root directory';
        } else {
            $failed[] = '".htaccess" should be available in root directory';
        }
    }

    if ($_POST['db_type'] == 'create-new-database') {
        $_POST['db_name'] = $_POST['cp_user'] . '_' . $_POST['db_name'];
        $_POST['db_user'] = $_POST['cp_user'] . '_' . $_POST['db_user'];
    }

    if ($action == 'result') {
        $url               = 'https://license.viserlab.com/install';
        $params            = $_POST;
        $params['product'] = $itemName;
        $ch                = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($result, true);

        if (@$response['error'] == 'ok' && $_POST['db_type'] == 'create-new-database') {
            try {

                $cpanelusername = $_POST['cp_user'];
                $cpanelpassword = $_POST['cp_password'];
                $domain         = $_SERVER['HTTP_HOST'];
                $authHeader[0]  = "Authorization: Basic " . base64_encode($cpanelusername . ":" . $cpanelpassword) . "\n\r";

                $dbname   = $_POST['db_name'];
                $username = $_POST['db_user'];
                $password = $_POST['db_pass'];

                //check secure password
                checkSecurePassword($password);

                // Create the database
                $cpError       = "cPanel not detected.";
                $createDbQuery = "https://$domain:2083/json-api/cpanel?cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adddb&cpanel_jsonapi_apiversion=1&arg-0=$dbname";

                $createDbCurl = curl_init();
                curl_setopt($createDbCurl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($createDbCurl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($createDbCurl, CURLOPT_HEADER, 0);
                curl_setopt($createDbCurl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($createDbCurl, CURLOPT_HTTPHEADER, $authHeader);
                curl_setopt($createDbCurl, CURLOPT_URL, $createDbQuery);
                $createDbResult = curl_exec($createDbCurl);
                $createDbResult = json_decode($createDbResult);
                echo "</pre>";
                $createDbError = @$createDbResult->data->error ?? @$createDbResult->data->reason ?? @$createDbResult->error;
                if ($createDbResult == false) {
                    throw new Exception($cpError);
                } else if ($createDbError) {
                    $cpError = $createDbError ?? $cpError;
                    $cpError = @$createDbResult->data->reason ? "Error from cPanel: " . $cpError : $cpError;
                    throw new Exception($cpError);
                }
                curl_close($createDbCurl);

                // Create the user and assign privileges
                $cpError        = "cPanel not detected.";
                $createUserCurl = curl_init();
                curl_setopt($createUserCurl, CURLOPT_URL, "https://$domain:2083/json-api/cpanel");
                curl_setopt($createUserCurl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($createUserCurl, CURLOPT_ENCODING, '');
                curl_setopt($createUserCurl, CURLOPT_MAXREDIRS, 10);
                curl_setopt($createUserCurl, CURLOPT_TIMEOUT, 0);
                curl_setopt($createUserCurl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($createUserCurl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($createUserCurl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($createUserCurl, CURLOPT_HTTPHEADER, $authHeader);
                curl_setopt(
                    $createUserCurl,
                    CURLOPT_POSTFIELDS,
                    array(
                        'cpanel_jsonapi_module'     => 'Mysql',
                        'cpanel_jsonapi_func'       => 'adduser',
                        'cpanel_jsonapi_apiversion' => '1',
                        'arg-0'                     => $username,
                        'arg-1'                     => $password,
                    )
                );
                $createUserResult = curl_exec($createUserCurl);

                $createUserResult = json_decode($createUserResult);
                $createUserError  = @$createUserResult->data->error ?? @$createUserResult->data->reason ?? @$createUserResult->error;
                if ($createUserResult == false) {
                    throw new Exception($cpError);
                } else if ($createUserError) {
                    $cpError = $createUserError ?? $cpError;
                    $cpError = @$createUserResult->data->reason ? "Error from cPanel: " . $cpError : $cpError;
                    throw new Exception($cpError);
                }
                curl_close($createUserCurl);

                // Assign the database to the user
                $cpError           = "cPanel not detected.";
                $createDbUserQuery = "https://$domain:2083/json-api/cpanel?cpanel_jsonapi_module=Mysql&cpanel_jsonapi_func=adduserdb&cpanel_jsonapi_apiversion=1&arg-0=$dbname&arg-1=$username&arg-2=ALL";

                $assignCurl = curl_init();
                curl_setopt($assignCurl, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($assignCurl, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($assignCurl, CURLOPT_HEADER, 0);
                curl_setopt($assignCurl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($assignCurl, CURLOPT_HTTPHEADER, $authHeader);
                curl_setopt($assignCurl, CURLOPT_URL, $createDbUserQuery);
                $assignDbResult = curl_exec($assignCurl);

                $assignDbResult = json_decode($assignDbResult);
                $assignError    = @$assignDbResult->data->error ?? @$assignDbResult->data->reason ?? @$assignDbResult->error;
                if ($assignDbResult == false) {
                    throw new Exception($cpError);
                } else if ($assignError) {
                    throw new Exception("There is an issue with assigning the user to the database.");
                }
                curl_close($assignCurl);
            } catch (Exception $e) {
                $response['error']   = 'error';
                $response['message'] = $e->getMessage();
            }
        }

        if (@$response['error'] == 'ok') {
            try {
                $db     = new PDO("mysql:host=$_POST[db_host];dbname={$_POST['db_name']}", $_POST['db_user'], $_POST['db_pass']);
                $dbinfo = $db->query('SELECT VERSION()')->fetchColumn();

                $engine  = @explode('-', $dbinfo)[1];
                $version = @explode('.', $dbinfo)[0] . '.' . @explode('.', $dbinfo)[1];

                if (strtolower($engine) == 'mariadb') {
                    if (!version_compare($version, '10.6', '>=')) {
                        $response['error']   = 'error';
                        $response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MariaDB ' . $version;
                    }
                } else {
                    if (!version_compare($version, '8.0', '>=')) {
                        $response['error']   = 'error';
                        $response['message'] = 'MariaDB 10.6+ Or MySQL 8.0+ Required. <br> Your current version is MySQL ' . $version;
                    }
                }
            } catch (Exception $e) {
                $response['error']   = 'error';
                $response['message'] = $_POST['db_type'] == 'create-new-database' ? 'There is a problem with creating the database.' : 'Database Credential is Not Valid';
            }
        }

        if (@$response['error'] == 'ok') {
            try {
                $query = file_get_contents("database.sql");
                $stmt  = $db->prepare($query);
                $stmt->execute();
                $stmt->closeCursor();
            } catch (Exception $e) {
                $response['error']   = 'error';
                $response['message'] = 'Problem Occurred When Importing Database!<br>Please Make Sure The Database is Empty.';
            }
        }

        if (@$response['error'] == 'ok') {
            try {
                $file = fopen($response['location'], 'w');
                fwrite($file, $response['body']);
                fclose($file);
            } catch (Exception $e) {
                $response['error']   = 'error';
                $response['message'] = 'Problem Occurred When Writing Environment File.';
            }
        }

        if (@$response['error'] == 'ok') {
            try {
                $db->query("UPDATE admins SET email='" . $_POST['email'] . "', username='" . $_POST['admin_user'] . "', password='" . password_hash($_POST['admin_pass'], PASSWORD_DEFAULT) . "' WHERE username='admin'");
            } catch (Exception $e) {
                $response['message'] = 'EasyInstaller was unable to set the credentials of admin.';
            }
        }
    }
    $sectionTitle = empty($action) ? 'Terms of Use' : $action;
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Easy Installer by ViserLab</title>
	<link rel="stylesheet" href="../assets/global/css/bootstrap.min.css">
	<link rel="stylesheet" href="../assets/global/css/all.min.css">
	<link rel="stylesheet" href="../assets/global/css/installer.css">
	<link rel="shortcut icon" href="https://license.viserlab.com/external/favicon.png" type="image/x-icon">
</head>

<body>
	<header class="py-3 border-bottom border-primary bg--dark">
		<div class="container">
			<div class="d-flex align-items-center justify-content-between header gap-3">
				<img class="logo" src="https://license.viserlab.com/external/logo.png" alt="ViserLab">
				<h3 class="title">Easy Installer</h3>
			</div>
		</div>
	</header>
	<div class="installation-section padding-bottom padding-top">
		<div class="container">
			<div class="installation-wrapper">
				<div class="install-content-area">
					<div class="install-item">
						<h3 class="title text-center"><?php echo $sectionTitle; ?></h3>
						<div class="box-item">
							<?php
                                if ($action == 'result') {
                                    echo '<div class="success-area text-center">';
                                    if (@$response['error'] == 'ok') {
                                        echo '<h2 class="text-success text-uppercase mb-3">Your system has been installed successfully!</h2>';
                                        if (@$response['message']) {
                                            echo '<h5 class="text-warning mb-3">' . $response['message'] . '</h5>';
                                        }
                                        echo '<p class="text-primary lead my-5 review-alert">Please rate us 5 stars on CodeCanyon if you found our installation process hassle-free and easy.</p>';

                                        echo '<p class="text-danger lead my-5">Please delete the "install" folder from the server.</p>';
                                        echo '<div class="warning"><a href="' . appUrl() . '" class="theme-button choto">Go to website and Activate</a></div>';
                                    } else {
                                        if (@$response['message']) {
                                            echo '<h3 class="text-danger mb-3">' . $response['message'] . '</h3>';
                                        } else {
                                            echo '<h3 class="text-danger mb-3">Your Server is not Capable to Handle the Request.</h3>';
                                        }
                                        echo '<div class="warning mt-2"><h5 class="mb-4 fw-normal">Try again. Or you can ask for support by creating a support ticket.</h5><a href="?action=information" class="theme-button choto me-1 mb-3">Try Again</a> <a href="https://viserlab.com/support" target="_blank" class="theme-button choto ms-1">create  ticket</a></div>';

                                    }
                                    echo '</div>';
                                } else if ($action == 'information') {
                                ?>
								<form action="?action=result" method="post" class="information-form-area mb--20">
									<div class="info-item">
										<h5 class="font-weight-normal mb-2">Website URL</h5>
										<div class="row">
											<div class="information-form-group col-12">
												<input name="url" value="<?php echo appUrl(); ?>" type="text" required>
											</div>
										</div>
									</div>
									<div class="info-item">
										<h5 class="font-weight-normal mb-2">Database Details</h5>
										<div class="row">
											<div class="information-form-group col-sm-12">
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="db_type" value="existing-database" id="existing-database" checked>
													<label for="existing-database">Existing Database </label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="db_type" value="create-new-database" id="create-new-database">
													<label for="create-new-database">
														Create New Database (for cPanel users only)
													</label>
												</div>
											</div>
											<div class="information-form-group col-sm-6 cpanel-credentials d-none">
												<input type="text" name="cp_user" placeholder="cPanel Username">
											</div>
											<div class="information-form-group col-sm-6 cpanel-credentials d-none">
												<input type="text" name="cp_password" placeholder="cPanel Password">
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_name" placeholder="Database Name" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_host" placeholder="Database Host" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input type="text" name="db_user" placeholder="Database User" required>
											</div>
											<div class="information-form-group col-sm-6">
												<input class="secure-password" type="text" name="db_pass" placeholder="Database Password">
												<small class="d-none text-danger weak-password-error"> Week password detected</small>
											</div>
										</div>
									</div>
									<div class="info-item">
										<h5 class="font-weight-normal mb-3">Admin Credential</h5>
										<div class="row">
											<div class="information-form-group col-lg-3 col-sm-6">
												<label>Username</label>
												<input name="admin_user" type="text" placeholder="Admin Username" required>
											</div>
											<div class="information-form-group col-lg-3 col-sm-6">
												<label>Password</label>
												<input name="admin_pass" type="text" placeholder="Admin Password" required>
											</div>
											<div class="information-form-group col-lg-6">
												<label>Email Address</label>
												<input name="email" placeholder="Your Email address" type="email" required>
											</div>
										</div>
									</div>
									<div class="info-item">
										<div class="information-form-group text-end">
											<button type="submit" class="theme-button choto">Install Now</button>
										</div>
									</div>
								</form>
								<script>
									"use strict";
									document.addEventListener('DOMContentLoaded', () => {
										const radioButtons = document.querySelectorAll('input[name="db_type"]');
										const cpanelCredentials = document.querySelectorAll('.cpanel-credentials');
										const inputFields = document.querySelectorAll('.cpanel-credentials input');
										var passwordInput = document.querySelector('input.secure-password');
										var inputPopup = document.createElement('div');

										radioButtons.forEach((radio) => {
											radio.addEventListener('change', (event) => {
												const isExistingDatabase = event.target.value === 'existing-database';
												cpanelCredentials.forEach((element) => {
													element.classList.toggle('d-none', isExistingDatabase);
												});
												inputFields.forEach((input) => {
													input.required = !isExistingDatabase;
												});
												isExistingDatabase || securePassword(passwordInput);
												isExistingDatabase && document.querySelector('.weak-password-error').classList.add('d-none');

												isExistingDatabase && document.querySelector('form [type="submit"]').removeAttribute('disabled');
											});
										});



										inputPopup.classList.add('input-popup');
										inputPopup.innerHTML = `
											<p class="error lower">1 small letter minimum</p>
											<p class="error capital">1 capital letter minimum</p>
											<p class="error number">1 number minimum</p>
											<p class="error special">1 special character minimum</p>
											<p class="error minimum">8 character password</p>
											<p class="success hash">Without the hash symbol (#)</p>
										`;
										var parentContainer = passwordInput.parentElement;
										parentContainer.appendChild(inputPopup);

										passwordInput.addEventListener('input', function() {
											var dbTypeInput = document.querySelector('input[name="db_type"]:checked');
											dbTypeInput.value === 'existing-database' || securePassword(this, inputPopup);
										});

										passwordInput.addEventListener('focus', function() {
											var dbTypeInput = document.querySelector('input[name="db_type"]:checked');
											dbTypeInput.value === 'existing-database' || inputPopup.parentNode.classList.add('hover-input-popup');
										});

										passwordInput.addEventListener('focusout', function() {
											var dbTypeInput = document.querySelector('input[name="db_type"]:checked');
											dbTypeInput.value === 'existing-database' || inputPopup.parentNode.classList.remove('hover-input-popup');
										});
									});

									function securePassword(input, inputPopup) {
										var weakPasswordErrorElement = document.querySelector('.weak-password-error');
										var password = input.value;
										if(!password){
											return false;
										}
										var capital = /[ABCDEFGHIJKLMNOPQRSTUVWXYZ]/;
										var capitalMatch = capital.test(password);
										var capitalElement = inputPopup?.querySelector('.capital');
										if (!capitalMatch) {
											capitalElement?.classList.remove('success');
											capitalElement?.classList.add('error');
										} else {
											capitalElement?.classList.remove('error');
											capitalElement?.classList.add('success');
										}

										var lower = /[abcdefghijklmnopqrstuvwxyz]/;
										var lowerMatch = lower.test(password);
										var lowerElement = inputPopup?.querySelector('.lower');
										if (!lowerMatch) {
											lowerElement?.classList.remove('success');
											lowerElement?.classList.add('error');
										} else {
											lowerElement?.classList.remove('error');
											lowerElement?.classList.add('success');
										}

										var number = /[1234567890]/;
										var numberMatch = number.test(password);
										var numberElement = inputPopup?.querySelector('.number');
										if (!numberMatch) {
											numberElement?.classList.remove('success');
											numberElement?.classList.add('error');
										} else {
											numberElement?.classList.remove('error');
											numberElement?.classList.add('success');
										}

										var special = /[`!@$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
										var specialMatch = special.test(password);
										var specialElement = inputPopup?.querySelector('.special');
										if (!specialMatch) {
											specialElement?.classList.remove('success');
											specialElement?.classList.add('error');
										} else {
											specialElement?.classList.remove('error');
											specialElement?.classList.add('success');
										}

										var minimum = password.length;
										var minimumElement = inputPopup?.querySelector('.minimum');
										if (minimum < 8) {
											minimumElement?.classList.remove('success');
											minimumElement?.classList.add('error');
										} else {
											minimumElement?.classList.remove('error');
											minimumElement?.classList.add('success');
										}

										var hash = /[#]/;
										var hashMatch = hash.test(password);
										var hashElement = inputPopup?.querySelector('.hash');
										if (hashMatch) {
											hashElement?.classList.remove('success');
											hashElement?.classList.add('error');
										} else {
											hashElement?.classList.remove('error');
											hashElement?.classList.add('success');
										}

										var submitButton = document.querySelector('form [type="submit"]');
										if (!capitalMatch || !lowerMatch || !numberMatch || !specialMatch || minimum < 8 || hashMatch) {
											submitButton.setAttribute('disabled', 'true');
											weakPasswordErrorElement.classList.remove('d-none');
										} else {
											submitButton.removeAttribute('disabled');
											weakPasswordErrorElement.classList.add('d-none');
										}
									}
								</script>
							<?php
                                } else if ($action == 'requirements') {
                                    $btnText = 'View Detailed Check Result';
                                    if (count($failed)) {
                                        $btnText = 'View Passed Check';
                                        echo '<div class="item table-area"><table class="requirment-table">';
                                        foreach ($failed as $fail) {
                                            echo "<tr><td>$fail</td><td><i class='fas fa-times'></i></td></tr>";
                                        }
                                        echo '</table></div>';
                                    }
                                    if (!count($failed)) {
                                        echo '<div class="text-center"><i class="far fa-check-circle success-icon text-success"></i><h5 class="my-3">Requirements Check Passed!</h5></div>';
                                    }
                                    if (count($passed)) {
                                        echo '<div class="text-center my-3"><button class="btn passed-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePassed" aria-expanded="false" aria-controls="collapsePassed">' . $btnText . '</button></div>';
                                        echo '<div class="collapse mb-4" id="collapsePassed"><div class="item table-area"><table class="requirment-table">';
                                        foreach ($passed as $pass) {
                                            echo "<tr><td>$pass</td><td><i class='fas fa-check'></i></td></tr>";
                                        }
                                        echo '</table></div></div>';
                                    }
                                    echo '<div class="item text-end mt-3">';
                                    if (count($failed)) {
                                        echo '<a class="theme-button btn-warning choto" href="?action=requirements">ReCheck <i class="fas fa-sync-alt"></i></a>';
                                    } else {
                                        echo '<a class="theme-button choto" href="?action=information">Next Step <i class="fas fa-angle-double-right"></i></a>';
                                    }
                                    echo '</div>';
                                } else {
                                ?>
								<div class="item">
									<h4 class="subtitle">License to be used on one(1) domain(website) only!</h4>
									<p> The Regular license is for one website or domain only. If you want to use it on multiple websites or domains you have to purchase more licenses (1 website = 1 license). The Regular License grants you an ongoing, non-exclusive, worldwide license to make use of the item.</p>
								</div>
								<div class="item">
									<h5 class="subtitle font-weight-bold">You Can:</h5>
									<ul class="check-list">
										<li> Use on one(1) domain only. </li>
										<li> Modify or edit as you want. </li>
										<li> Translate to your choice of language(s).</li>
									</ul>
									<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> If any issue or error occurred for your modification on our code/database, we will not be responsible for that. </span>
								</div>
								<div class="item">
									<h5 class="subtitle font-weight-bold">You Cannot: </h5>
									<ul class="check-list">
										<li class="no"> Resell, distribute, give away, or trade by any means to any third party or individual. </li>
										<li class="no"> Include this product into other products sold on any market or affiliate websites. </li>
										<li class="no"> Use on more than one(1) domain. </li>
									</ul>
								</div>
								<div class="item">
									<p class="info">For more information, Please Check <a href="https://codecanyon.net/licenses/faq" target="_blank">The License FAQ</a></p>
								</div>
								<div class="item text-end">
									<a href="?action=requirements" class="theme-button choto">I Agree, Next Step</a>
								</div>
							<?php
                                }
                            ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<footer class="py-3 text-center bg--dark border-top border-primary">
		<div class="container">
			<p class="m-0 font-weight-bold">&copy;<?php echo Date('Y') ?> - All Right Reserved by <a href="https://viserlab.com/">ViserLab LLC</a></p>
		</div>
	</footer>
	<script src="../assets/global/js/bootstrap.bundle.min.js"></script>
</body>
</html>