<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/png" href="./images/fablogo2.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTU Danao - FabLab</title>
</head>
<body>
    
    <div class="text">
    <img id="toplogo" src="./images/fablogo2.png">
    <h2>CTU FABRICATION LABORATORY</h2>
    <p>Danao Campus</p>
    </div>

<!--NAVIGATION BAR-->
<header>
    <div class="topnav" id="myTopnav">
        <img src="./images/logo.jpg" alt="Logo" class="logo-img"> 
        <a id="none" href="#section-one"><i  class="fa fa-home"></i> Home</a>
        <a id="none" href="#section-two"><i class="fa fa-info-circle"></i> About Us</a>
        <a id="none" href="#section-three"><i class="fa fa-info-circle"></i> Contact Us</a>

        <div class="right-links">
            <a href="php/logreg.php" class="split"><i class="fa fa-sign-in"></i> Login/Register</a>
        </div>

    </div>
</header>



    <div class="parallax">
        <div class="parallax__layer parallax__layer__1">
            <img src="./images/layer1.png" />
        </div>
        <div class="parallax__layer parallax__layer__2">
            <img src="./images/layer2.png" />
        </div>
        <div class="parallax__layer parallax__layer__3">
            <img src="./images/layer3.png" />
        </div>
        <div class="parallax__layer parallax__layer__4">
            <img src="./images/layer4.png" />
        </div>
        <!--lay5 removed-->
        <div class="parallax__layer parallax__layer__6">
            <img src="./images/layer6.png" />
        </div>
        
    <div class="parallax__cover">
        <div class="wrapper">
        <section id="section-one" class="one">
    <div class="image-grid">
        <div class="grid-item large"><img src="./images/proffab.jpg" alt="Fab Lab Image 1"></div>
        <div class="grid-item small"><img src="./images/goku.jpg" alt="Fab Lab Image 2"></div>
        <div class="grid-item wide"><img src="./images/fablab.jpg" alt="Fab Lab Image 3"></div>
    </div>
    <div class="about_text">   
        <h1>Welcome to the Fabrication Lab</h1>
        <p>Welcome to the CTU Danao FabLab, where innovation meets craftsmanship.
             We offer expert services in layout and design, tarpaulin printing, 3D printing, laser cutting,
              woodworking, embroidery, and custom sticker printing. Whether you're a student, entrepreneur,
               or a creator at heart, we provide the right tools and expertise to transform your ideas into reality. 
               Join us, and let's bring your vision to life with precision and creativity.</p>
        <div class="social">
            <a href="https://web.facebook.com/ctudanaofablab"><i class="fa fa-facebook"></i></a>
            <a href="https://www.ctu.edu.ph/danao/research/usf/fab-lab/"><i class="fa fa-arrow-right"></i></a>
        </div>
    </div>
</section>
        </div>

        <div class="wrapper">
            <section id="section-two" class="two">
            <div class="about_text"> 
                <h1>About the Fabrication Lab</h1>
                    <p>On September 19, 2019, Cebu Technological University - Danao Campus marked a significant milestone with the launch 
                        of its state-of-the-art Fabrication Laboratory (Durano, 2019). The CTU Danao FabLab has since become a hub of innovation,
                         offering advanced services such as layout and design, tarpaulin printing, 3D printing, laser cutting, woodworking,
                          embroidery, and custom sticker printing.</p>
            </div>
            <img id="image" class="comp-svg" src="./images/computer.svg">
            </section>
        </div>

        <div class="wrapper">
    <section id="section-three" class="three">
        <div class="contact">
        <h2>FabLab Personnel</h2>
            <!-- Top Row: Two Images -->
            <div class="top-row">
                <figure>
                    <img src="./images/ROMELITO N BAYNO.jpg" alt="Romelito Bayno">
                    <figcaption>Romelito N. Bayno Jr. - Director</figcaption>
                </figure>
                <figure>
                    <img src="./images/MARSAN P DUA.jpg" alt="Marsan Dua">
                    <figcaption>Marsan P. Dua - Operations Manager</figcaption>
                </figure>
            </div>
            
            <!-- Bottom Row: Three Columns -->
            <div class="bottom-row">
                <figure>
                    <img src="./images/blank.jpg" alt="Image Placeholder">
                    <figcaption>Vincent Jay Bataluna - Operations Assistant</figcaption>
                </figure>
                <figure>
                    <img src="./images/KENNETH WEN.jpg" alt="Kenneth Wen">
                    <figcaption>Kenneth Wen - Operations Assistant</figcaption>
                </figure>
                <figure>
                    <img src="./images/blank.jpg" alt="Image Placeholder">
                    <figcaption>Bobby Buot - Operations Assistant</figcaption>
                </figure>
            </div>
        </div>
    </section>
</div>



        <!-- Temporary Footer -->
        <footer>
            <p>&copy; 2024 Fabrication Lab. All rights reserved.</p>
        </footer>
    </div> 
    </div>

    

    <script src="script.js"></script> <!-- Link to your script.js file -->
    <script>
        function toggleMenu() {
            var topnav = document.getElementById("myTopnav");
            if (topnav.className === "topnav") {
                topnav.className += " responsive"; // Adds "responsive" class to show menu
            } else {
                topnav.className = "topnav"; // Removes "responsive" class to hide menu
            }
        }
    </script>


</body>
</html>