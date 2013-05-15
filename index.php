<?php
include 'edition.php';
?>
<!DOCTYPE html>
<html lang="ase">
  <head>
    <meta charset="utf-8">
    <title>SignWriting Icon Server</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Server side images and more for SignWriting">
    <meta name="author" content="Stephen E Slevinski Jr">

    <!-- Le styles -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/index.css" rel="stylesheet">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    <link href="css/prettify.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="apple-touch-icon-57-precomposed.png">
    <link rel="shortcut icon" href="http://signpuddle.com/favicon.ico">

    <!-- Universal SignWriting Pluging, Viewer Script -->
<?php 
      $viewer = @$_REQUEST['viewer'];
      if (!$viewer) $viewer = "styled";
      switch ($viewer){
        case "styled":
          echo '    <script type="text/javascript">' . "\n";
          echo $styled_script . "\n";
          echo $styled_start . "\n";
          echo '    </script>' . "\n";
          break;
        case "thin":
          echo '    <script type="text/javascript">' . "\n";
          echo $thin_script . "\n";
          echo $thin_start . "\n";
          echo '    </script>' . "\n";
          break;
        case "font":
          echo '    <script type="text/javascript" src="js/signwriting_text.js"></script>' . "\n";
          echo '    <script type="text/javascript" src="js/signwriting_font.js"></script>' . "\n";
      }
    ?>

  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="http://signpuddle.com"><img class="logo" src="img/logo.png" alt="Open SignPuddle logo" border="0"></a>
          <a class="brand" href="#">SignWriting Icon Server<span><br><?php echo $swis_edition;?><br>Semver: <?php echo $semver;?></span>
          </a>
          <div class="nav-collapse collapse">
            <ul class="nav pull-right">
              <li <?php if ($viewer=="styled") echo 'class="active"';?> ><a href="?viewer=styled">Styled Viewer L533x532S1bd10505x502S1bd18479x502S28809513x473S28811467x472S22100502x495S22100488x495S20500496x483S2fb00494x469</a></li>
              <li <?php if ($viewer=="thin") echo 'class="active"';?> ><a href="?viewer=thin">Thin Viewer L525x529S15a19496x506S1c513475x494S20e00499x484S26507512x471</a></li>
              <li <?php if ($viewer=="font") echo 'class="active"';?>><a href="?viewer=font">Font Viewer L533x515S1c510503x486S1c518467x485S20600490x504</a></li>
            </ul>
            <ul class="nav nav-options">
              <li><a href="https://github.com/Slevinski/swis/archive/master.zip">Download Source</a></li>
              <li><a href="https://github.com/Slevinski/swis">GitHub Repository</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Text M538x517S19202505x483S1920a473x483S26606508x500S26612461x501S20500493x496</li>
              <li><a href="#formal">Formal SignWriting</a></li>
              <li class="nav-header">Servers M548x530S15a37500x491S15a3f478x507S26c07521x474S26c11461x490S2fb00487x476S2f900537x470S2f900452x488</li>
              <li><a href="#infrastructure">Infrastructure</a></li>
              <li><a href="#labs">Wikimedia Labs</a></li>
              <li><a href="#plugins">Plugins</a></li>
              <li class="nav-header">Websites M526x526S18720475x475S23504478x508S26606496x501</li>
              <li><a href="#styled">SignWriting Styled Viewer</a></li>
              <li><a href="#thin">SignWriting Thin Viewer</a></li>
              <li><a href="#font">SignWriting Font Viewer</a></li>
              <li><a href="#mediawiki">SignWriting MediaWiki Plugin</a></li>
              <li class="nav-header">End Users M545x518S30007482x483S16d10516x472S20600523x496</li>
              <li><a href="#portable">Portable Bookmark</a></li>
              <li><a href="#pastable">Pastable Bookmark</a></li>
              <li><a href="#template">Templates</a></li>
              <li class="nav-header">References M536x529S19a20508x509S19a28463x509S2fb00491x471S2e742464x481S2e732508x480</li>
              <li><a href="#draft">Internet Draft</a></li>
              <li><a href="#theory">Theory and Example</a></li>
              <li><a href="#websites">Websites and more</a></li>
              <li class="nav-header" style="font-family:iswa;"><br>Local Font M536x516S17719506x492S17719464x496S2d628469x487S2d628507x483 󽠃󽼒󽼝󽡼󽠒󽠠󽻭󽻣󽦡󽠐󽠦󽼃󽻵 󽠃󽼗󽼢󽨚󽠔󽠨󽻯󽼉󽠰󽠑󽠡󽼂󽻝󽨚󽠐󽠤󽼈󽻿󽠰󽠑󽠩󽻩󽻦</li>
              <li><a href="#truetype">TrueType Font</a></li>
              <li><a href="#graphite">Graphite</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <h1>SignWriting Icon Server</h1>
            <h1>L523x535S10019478x475S10011502x466S2ea04507x502S2ea48485x510 L517x532S1eb20493x485S15a0a484x469S29b0b483x497 L526x536S19210504x465S19218475x465S2930c512x489S29314474x489S2fb04493x530 L548x530S15a37500x491S15a3f478x507S26c07521x474S26c11461x490S2fb00487x476S2f900537x470S2f900452x488</h1>
          </div>

          <!-- formal
          ================================================== -->
          <hr>
          <section id="text">
            <div class="page-header">
              <h1>SignWriting Text</h1>
              <h2 lang="ase">M538x517S19202505x483S1920a473x483S26606508x500S26612461x501S20500493x496</h2>
              <p>SignWriting Text uses strings of characters to form words that represent signs.
              </p> 
            </div>

            <h3 id="formal">Formal SignWriting</h3>
            <p>Formal SignWriting uses ASCII strings to represent the logographic names of the signs. 
            </p>
            <textarea id="fsw_text">M518x529S14c20481x471S27106503x489 M518x533S1870a489x515S18701482x490S20500508x496S2e734500x468 S38800464x496</textarea>
            <a id="process" class="btn btn-large btn-info">Process L532x538S18040501x523S18048467x511S2eb4c477x470S2eb08506x483S2fb00494x462</a>
            <br><br>
            <div id="output"></div>

          </section>


          <!-- Servers
          ================================================== -->
          <hr>
          <section id="servers">
            <div class="page-header">
              <h1>Servers</h1>
              <h2 lang="ase">M548x530S15a37500x491S15a3f478x507S26c07521x474S26c11461x490S2fb00487x476S2f900537x470S2f900452x488</h2>
            </div>

            <h3 id="infrastructure">Infrastructure</h3>
            <p>Until the TrueType Font is perfected, using the SignWriting script on computers requires a SignWriting Icon Server to generate the PNG and SVG images.</p>
            <p>The SignWriting Icon Server is an open source project that is easy to install with very modest requirements of PHP, PDO SQLite, and the GD graphics library.</p>
            <p>Each SignWriting Icon Server provides static or customized versions of the available <a href="#plugins">plugins</a>.</p>

            <h3 id="labs">Wikimedia Labs</h3>
            <p>The <a href="http://swis.wmflabs.org">main public SignWriting Icon Server</a> is available on <a href="https://wikitech.wikimedia.org">Wikimedia Labs</a>.</p>
            <a href="http://swis.wmflabs.org"><h3 lang="ase">M549x543S18527530x497S1852f451x497S14c20509x457S14c28468x457S26610472x491S26600512x491S28c0e514x522S28c16458x522 M529x530S10030511x500S10038476x500S2a200506x471S2a218472x471 M532x525S10004512x475S22f04507x511S1000c474x475S22f14469x511</h3></a>
            </p>

            <h3 id="plugins">Plugins</h3>
            <p>The plugins automatically convert Formal SignWriting strings into grammatically correct SignWriting Text.
            </p>
            <p>Each SignWriting Icon Server provides several plugins to support SignWriting Text.
            The customized plugins embed the server url in the code.
            The static plugins use the experimental <a href="#truetype">TrueType Font</a> rather than the server side images.
            </p>
            <p>The <a href="#styled">SignWriting Styled Viewer</a> uses jQuery, HTML and CSS to create styled SignWriting Text.
            </p>
            <p>The <a href="#thin">SignWriting Thin Viewer</a> is a micro library written in 1.5K of stand alone JavaScript.
            </p>
            <p>The <a href="#font">SignWriting Font Viewer</a> is written in JavaScript and uses temporary Unicode characters on plane 15.
            The font can be locally installed or streamed from a server.
            </p> 
            <p>The <a href="#mediawiki">SignWriting MediaWiki Plugin</a> is a custom extension for MediaWiki software.
            </p> 
          </section>


          <!-- For web sites
          ================================================== -->
          <hr>
          <section id="website">
            <div class="page-header">
              <h1>For Web Sites</h1>
              <h2 lang="ase">M526x526S18720475x475S23504478x508S26606496x501</h2>
              <p>Each SignWriting Icon Server can support an arbitrary number of websites.  This server uses the following URL: <?php echo '<a href="' . $swis_url. '">' . $swis_url . '</a>';?>
            </div>

            <h3 id="styled">SignWriting Styled Viewer</h3>
            <p>The SignWriting Styled Viewer is written in JavaScript and requires the jQuery library for CSS integration.
            <pre class=""><ol class="linenums">
            <li class="L1"><span class="dec">&lt;script type="text/javascript" src="<a href="js/jquery.js">js/jquery.js</a>"&gt;</span></li>
            <li class="L1"><span class="dec">&lt;script type="text/javascript" src="<a href="js/signwriting_styled.php">js/signwriting_styled.js</a>"&gt;</span></li>
            </ol></pre>

            <h3 id="thin">SignWriting Thin Viewer</h3>
            <p>The SignWriting Thin Viewer is a micro library written in 1.5K of stand alone JavaScript.
            <pre class=""><ol class="linenums">
            <li class="L1"><span class="dec">&lt;script type="text/javascript" src="<a href="js/signwriting_thin.php">js/signwriting_thin.js</a>"&gt;</span></li>
            </ol></pre>
            <h4 id="thin_template">HTML Template</h4>
            <p>The <a href="signwriting_template.zip">SignWriting HTML Template</a> includes two small files: a nearly blank HTML document and the SignWriting Thin Viewer.
            <p>The <a href="signwriting_bootstrap.zip">SignWriting Bootstrap Template</a> includes HTML, CSS, and JavaScript files.  It is an adaptive template using SignWriting built on top of a Twitter Bootstrap example.</p>

            <h3 id="font">SignWriting Font Viewer</h3>
            <p>The SignWriting Font Viewer is written in JavaScript and uses temporary Unicode characters on plane 15.
             The font can be locally installed or streamed from a server.
             </p>
             <pre class=""><ol class="linenums">
            <li class="L1"><span class="dec">&lt;script type="text/javascript" src="<a href="js/signwriting_text.js">js/signwriting_text.js</a>"&gt;</span></li>
            <li class="L1"><span class="dec">&lt;script type="text/javascript" src="<a href="js/signwriting_font.js">js/signwriting_font.js</a>"&gt;</span></li>
            </ol></pre>
          
            <h3 id="mediawiki">SignWriting MediaWiki Plugin</h3>
            <p>The SignWriring MediaWiki Plugin is a custom extension for MediaWiki software.  Utilizing the SignWriting Icon Server available on Wikimedia Labs, this extension adds a single client side script to the Resource Module before the page view.  Except to pass a 3 KB file to the client, the server running the MediaWiki software is not involved.</p>
            <ul><li><a href="http://www.mediawiki.org/wiki/Extension:SignWriting_MediaWiki_Plugin">Extension page on MediaWiki.org</a></li>
            <li><a href="https://gerrit.wikimedia.org/r/gitweb?p=mediawiki/extensions/SignWritingMediaWikiPlugin.git">Code base in Wikimedia's gerrit</a></li>
            </ul>
          </section>


          <!-- For End Users
          ================================================== -->
          <hr>
          <section id="end-users">
            <div class="page-header">
              <h1>For End Users</h1>
              <h2 lang="ase">M545x518S30007482x483S16d10516x472S20600523x496</h2>
            </div>

            <h3 id="portable">Portable Bookmark</h3>
            <p>Bookmark this link to the <a href="<?php echo htmlentities($thin_bookmark);?>">SignWriting Thin Viewer</a>.
            For easier access, place this bookmark on the toolbar.  You may be able to drag the link onto the bookmark toolbar, if the toolbar is visible.</p>
            <p>Use anywhere online where you find the ASCII code of Formal SignWriting.</p>

            <h3 id="pastable">Pastable Bookmark</h3>
            <p>Create or edit a bookmark with the following line of Javascript as the location or url of the bookmark.</p>
            <pre class=""><ol class="linenums">
            <li class="L0"><span class="dec"><?php echo htmlentities($thin_bookmark);?></span></li>
            </ol></pre>

            <h3 id="template">Templates</h3>
            <p>The <a href="signwriting_template.zip">SignWriting HTML Template</a> includes two small files: a nearly blank HTML document and the SignWriting Thin Viewer.
            <p>The <a href="signwriting_bootstrap.zip">SignWriting Bootstrap Template</a> includes HTML, CSS, and JavaScript files.  It is an adaptive template using SignWriting built on top of a Twitter Bootstrap example.</p>
          </section>

          <!-- References 
          ================================================== -->
          <hr>
          <section id="references">
            <div class="page-header">
              <h1>References</h1>
              <h2 lang="ase">M536x529S19a20508x509S19a28463x509S2fb00491x471S2e742464x481S2e732508x480</h2>
            </div>

            <h3 id="draft">Internet Draft to become RFC</h3>
            <p>Submitted to the IETF, <a href="http://signpuddle.net/wiki/index.php/I-D_draft-slevinski-signwriting-text">draft-slevinski-signwriting-text</a> is available for review. </p>

            <h3 id="theory">Theory and Example</h3>
            <p><a href="http://signpuddle.net/wiki/index.php/MSW">Modern SignWriting</a> explains the character encoding model of SignWriting Text, stable since January 12th, 2012.
            </p>
            <h3 id="websites">Websites and more</h3>
            <ul>
              <li class="">
                <a href="http://www.signwriting.org/lessons/">SignWriting Lessons</a>
              </li>
              <li class="">
                <a href="http://www.signwriting.org/forums/swlist/">Email List</a>
              </li>
              <li class="">
                <a href="http://signpuddle.org">SignPuddle Online</a>
              </li>
            </ul>
          </section>

          <!-- local font 
          ================================================== -->
          <hr>
          <section id="font">
            <div class="page-header">
              <h1>Local Font</h1>
              <p>The TrueType Font implementation is not production ready yet.  The proof of concept has several issues and is prone to crashing.  Development continues...</p>
              <br>
              <h2 lang="ase" style="font-family:iswa;">󽠃󽼒󽼝󽡼󽠒󽠠󽻭󽻣󽦡󽠐󽠦󽼃󽻵 󽠃󽼗󽼢󽨚󽠔󽠨󽻯󽼉󽠰󽠑󽠡󽼂󽻝󽨚󽠐󽠤󽼈󽻿󽠰󽠑󽠩󽻩󽻦</h2>
              <p lang="ase" style="font-family:iswa;">󽠃󽼗󽼢󽨚󽠔󽠨󽻯󽼉󽠰󽠑󽠡󽼂󽻝󽨚󽠐󽠤󽼈󽻿󽠰󽠑󽠩󽻩󽻦</p>
            </div>

            <h3 id="truetype">TrueType Font</h3>
            <p>Download and install the TrueType Font</p>
            <p>Smart font development by Eduardo Trapani:  
            <a href="http://signpuddle.net/iswa/iswa.ttf">iswa.ttf</a>, 6.1MB
            <a href="https://github.com/bidaian/iswa_graphite">Github Source</a>
            </p>

            <h3 id="graphite">Graphite</h3>
            <p>The TrueType Font works with <a href="http://scripts.sil.org/cms/scripts/page.php?site_id=projects&item_id=graphite_home">Graphite</a>
            </p>
          
            <h3>Enable Graphite in FIrefox</h3>
            <p>Enable Graphite in the Firefox Browser for testing and development: <a href="http://scripts.sil.org/cms/scripts/page.php?site_id=projects&item_id=graphite_firefox">instructions</a>
            </p>

          </section>

        </div>
      </div>

      <hr>

      <footer>
        <p>SignPuddle Standard: SignWriting Icon Server</p>
        <p>Copyright 2007-2013 Stephen E Slevinski Jr. Some Rights Reserved.</p>
        <p>Except where otherwise noted, this work is licensed under</p>
        <p><a href="http://creativecommons.org/licenses/by-sa/3.0/">Creative Commons Attribution ShareAlike 3.0</p>
      </footer>

    </div><!--/.fluid-container-->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap-transition.js"></script>
    <script src="js/bootstrap-alert.js"></script>
    <script src="js/bootstrap-modal.js"></script>
    <script src="js/bootstrap-dropdown.js"></script>
    <script src="js/bootstrap-scrollspy.js"></script>
    <script src="js/bootstrap-tab.js"></script>
    <script src="js/bootstrap-tooltip.js"></script>
    <script src="js/bootstrap-popover.js"></script>
    <script src="js/bootstrap-button.js"></script>
    <script src="js/bootstrap-collapse.js"></script>
    <script src="js/bootstrap-carousel.js"></script>
    <script src="js/bootstrap-typeahead.js"></script>
    <script type="text/javascript">
      jQuery('a#process').click(function(){
        jQuery('div#output').html('<h3>Processed Output</h3>' + jQuery('textarea#fsw_text').val());
        signwriting_<?php echo $viewer;?>(document.getElementById("output"));
      });
    </script>

  

</body></html>