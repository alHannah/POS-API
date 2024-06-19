<!DOCTYPE html>
<html>
    <head>
    	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    	<style type="text/css">
    		* {
				font-family: "Poppins", sans-serif;
			}
            body {
                width: 100%;
                margin: 0;
            }
    		div {
                height: 45vh;
                background-color: #101c4d;
    			padding: 4rem 4rem 8rem 4rem;
    			text-align: center;
                position: relative;
    		}
    		div .logo-img {
    			height: 8rem;
    			margin-bottom: 1rem;
    		}
    		div p {
    			font-size: 24px;
    			color: rgba(255,255,255,.7);
    			margin: .5rem 0;
    		}
    		div h1 {
    			font-size: 46px;
                color: white;
    			margin: .5rem 0;
    		}
            div .curve-img {
                width: 100%;
                position: absolute;
                bottom: 0;
                left: 0;
                height: 18vh;
            }
            .copyright {
                width: 100%;
                text-align: center;
                font-size: 12px;
                color: rgba(0,0,0,.4);
                position: fixed;
                bottom: 1rem;
            }
    	</style>

        <title>NexBridge Technologies Inc.</title>
    </head>
    <body>
    	<div>
    		<img class="logo-img" src="/nexbridge.png" alt="nexbridge-logo">
    		<p>Welcome to Nexbridge Technologies Inc.</p>
    		<h1><?php echo $api; ?> API</h1>

    		<img class="curve-img" src="/curve.png">
    	</div>
        <p class="copyright">© <?php echo date("Y"); ?> Nexbridge Technologies Inc.</p>
    </body>
</html>