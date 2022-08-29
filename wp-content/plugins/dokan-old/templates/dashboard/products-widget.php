<?php

/**
 * Dokan Dashboard Template
 *
 * Dokan Dashboard Product widget template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>


<style>
.awhite, .awhite:hover {color: white;}

.tdorder {border-radius: 6px;
        width: 50px;
        color: white;
        padding-bottom: 0;
        /* text-align: center; */}

.tborder {border-spacing: 10px;}

.btnwhite { background-color: #ebeaea75;
                width: 100%;
                padding: 0;
                margin: 4px 0;}

.btnwhite:hover {background-color: #ebeaeab3; }

@media screen and (max-width: 600px) {	

    /* table {width:100%;} */

    /* thead {display: none;} */

    tbody td {display: inline-block; width: 150px!important; height: 100px; margin:5px;}

    .tborder {border-spacing: 0;}
    /* tbody td:before {

        content: attr(data-th);

        display: block;

        text-align:center; 

    } */

}
</style>

<div style="padding: 0 15px">
    <h2 style="display:inline">إدارة المنتجات</h2>
    <span class="pull-right">
        <a style="color:black; font-weight:bold" href="<?php echo esc_url( dokan_get_navigation_url( 'new-product' ) ); ?>"><?php esc_html_e( '+ إضافة منتج جديد', 'dokan-lite' ); ?></a>
    </span>
</div>

<table class="tborder">
    <tbody>
    <tr style="height: 80px;">
        
        <td class="tdorder" style=" padding-right: .5em!important;
                    background-color: #133E66">
                <a class="awhite" href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'publish' ), $products_url ) ); ?>">
                    <div style="font-size: 25px;" class="count"><?php echo esc_attr( $post_counts->publish ); ?></div>
                    <span class="title"><?php esc_html_e( 'الفعالة', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

        <td class="tdorder" style=" 
                    background-color: #133E66">
                <a class="awhite" href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'pending' ), $products_url ) ); ?>">
                    <div style="font-size: 25px;" class="count"><?php echo esc_attr( $post_counts->pending ); ?></div>
                    <span class="title"><?php esc_html_e( 'بانتظار المراجعة', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?> 
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

        <td class="tdorder" style=" 
                    background-color: #133E66">
                <a class="awhite" href="<?php echo esc_url( add_query_arg( array( 'post_status' => 'draft' ), $products_url ) ); ?>">
                    <div style="font-size: 25px;" class="count"><?php echo esc_attr( $post_counts->draft ); ?></div>
                    <span class="title"><?php esc_html_e( 'المرفوضة', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

        <td class="tdorder" style=" padding-left: .5em!important;
                    background-color: #133E66">
                <a class="awhite" href="<?php echo esc_url( $products_url ); ?>">
                    <div style="font-size: 25px;" class="count"><?php echo esc_attr( $post_counts->total ); ?></div>
                    <span class="title"><?php esc_html_e( 'كل المنتجات', 'dokan-lite' ); ?></span> 
                    <button class="btnwhite">
                    <?php esc_attr_e( 'إظهر التفاصيل', 'dokan-lite' ); ?>
                    <span style="">&#62;</span>
                    </button>
                </a>
        </td>

    </tr>
    </tbody>
</table>

