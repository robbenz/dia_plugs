<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
        <style type="text/css">
            body {
                color: #000;
                font-family: "lato", "helvetica", "open sans", "sans-serif";
            }
            .logo{
                width: 112px; float:left; margin-right:5px;
            }
            .right{
                float: right;
                width: 40%;
                text-align: right;
            }
            .clear{height:4px;
                clear: both;
            }
            .admin_info{
                font-size: 13px;

            }
            .diamedical{width:500px; float:left;}
            table{
                border: 0;
            }
            table.quote-table{
                border: 0;
                font-size: 14.5px;
            }

            .small-title{
                text-align: right;
                font-weight: 600;
                color: #4e4e4e;
                padding-top: 5px;
                padding-right: 5px;
            }
            .small-info p{
                border-left: 2px solid #00426a;
                padding: 0 0 5px 5px;
                margin-bottom: 20px;
            }
            .quote-table td{
                border: 0;
                border-bottom: 1px solid #464444;
            }
            .quote-table .with-border td{
                border-bottom: 2px solid #464444;
            }
            .quote-table .with-border td{
                border-top: 2px solid #464444;
            }
            .quote-table .quote-total td{
                height: 100px;
                vertical-align: middle;
                font-size: 18px;
                border-bottom: 0;
            }
            .quote-table small{
                font-size: 13px;
            }
            .quote-table .last-col{
                padding-right: 45px;
            }
            .quote-table .last-col.tot{
                font-weight: 600;
            }
            .quote-table .tr-wb{
                border-left: 1px solid #464444 ;
                border-right: 1px solid #464444 ;
            }
            .pdf-button{
                color: #a8c6e4;
                text-decoration: none;
            }
            div.content{ padding-bottom: 100px; border-bottom: 1px }

            .footer {
                position: fixed;
                bottom: 0;
                text-align: center;
            }

            .footer {
                width: 100%;
                text-align: center;
                position: fixed;
                bottom:60px;
            }

            .pagenum:before {
                content: counter(page);
            }
        </style>
        <?php

        do_action( 'yith_ywraq_quote_template_head' );
        ?>
    </head>

    <body>
    <?php
        do_action( 'yith_ywraq_quote_template_footer', $order_id );
    ?>

    <?php
        do_action( 'yith_ywraq_quote_template_header', $order_id );
    ?>
    <div class="content">
    <?php
        do_action( 'yith_ywraq_quote_template_content', $order_id );
    ?>
    </div>
    </body>
</html>
