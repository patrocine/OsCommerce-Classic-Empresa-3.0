<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2008 osCommerce

  Released under the GNU General Public License
*/
?>
<br />
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td align="center" class="smallText">
<?php
/*
  The following copyright announcement is in compliance
  to section 2c of the GNU General Public License, and
  can not be removed, or can only be modified
  appropriately with additional copyright notices.

  For more information please read the osCommerce
  Copyright Policy at:

  http://www.oscommerce.com/about/copyright

  This comment must be left intact together with the
  copyright announcement.
*/
?>
osCommerce Online Merchant Copyright &copy; 2010 <a href="http://www.oscommerce.com" target="_blank">osCommerce</a><br />
osCommerce provides no warranty and is redistributable under the <a href="http://www.fsf.org/licenses/gpl.txt" target="_blank">GNU General Public License</a>
    </td>
  </tr>
  <tr>
    <td><?php echo tep_image(DIR_WS_IMAGES . 'pixel_trans.gif', '', '1', '5'); ?></td>
  </tr>
  <tr>
    <td align="center" class="smallText">Powered by <a href="http://www.oscommerce.com" target="_blank">osCommerce</a></td>
  </tr>
</table>

  <p><font face="Verdana" size="1">



 <?php


 
          if (tep_session_is_registered('admin')) {

                                                 //$order->info['orders_status'] <> $cobrado
            if  (ACTIVO_CORREGIDOR == 'True' and $PHP_SELF == 'index.php'){




    $sumar_orders_sales_raw = "select count(orders_status) as value from   " . TABLE_ORDERS . " where orders_status = 0";
    $sumar_orders_sales_query = tep_db_query($sumar_orders_sales_raw);
    $sumar_orders= tep_db_fetch_array($sumar_orders_sales_query);



          echo  $sumar_orders['value'];
          
               $bo_or_pro_quanty_values = tep_db_query("select * from " . TABLE_ORDERS . " where orders_status = '" . 0 . "' order by orders_id DESC");
   while ($bo_or_pro = tep_db_fetch_array($bo_or_pro_quanty_values)){


        tep_db_query("delete from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" .  $bo_or_pro['orders_id'] . "'");


}


                   //     $sql_status_update_array = array('orders_status' => 33,);

             //tep_db_perform(TABLE_ORDERS, $sql_status_update_array, 'update', " orders_status = '" . 0 . "'");


              ?>
 <p><a href="<?php echo 'index.php?actualizar_cantidades=ok&comprobar_images=ok'; ?>" name="actcan">Actualizar Todo</a></p>

    <p>Productos que en la web est�n en negativos, <a href="<?php echo 'index.php?actualizar_cantidades=ok'; ?>" name="actcan">Actualizar Cantidades</a></p>
                 <?php

                                    if (ACTIVO_PRODUCTS_STATUS == 'True'){
                                   // $sql_products_status =  'and  p.products_status = '. SELEC_PRODUCTS_STATUS .'';
                                                                      }
                                    $limit = LIMIT_ACTUALIZAR_CANTIDADES;
                                    $limit_stock = LIMIT_STOCK_ACTCAN;
                                    
               $ayuda_quanty_values = tep_db_query("select * from " . TABLE_ORDERS . " where orders_status = '" . 75 . "' or orders_status = '" . 38 . "' order by orders_id DESC");
    $ayuda_quanty = tep_db_fetch_array($ayuda_quanty_values);


                $ayuda_producto_values = tep_db_query("select * from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . 'products_stock' . " ps where p.stock_nivel = 6 and p.products_id = ps.products_id and p.products_id = pd.products_id and ps.products_stock_real <= '" . $limit_stock . "' $sql_products_status order by time_entradasysalidas ASC LIMIT $limit");
    while ($ayuda_producto = tep_db_fetch_array($ayuda_producto_values)){
      $tr = $ayuda_producto['products_stock_real']-$ayuda_producto['products_stock_real']-$ayuda_producto['products_stock_real'];




                   $actualizar_cantidades = $_GET['actualizar_cantidades'];
    if ($actualizar_cantidades){                                                                                                                                                                                                                                                                         // p.stock_nivel = 6 and p.products_id = pd.products_id and p.time_entradasysalidas <= '" . 0 . "' and  p.products_status = '" . 1 . "'
                                                                                                                                                                                        // and p.time_entradasysalidas <= '" . 0 . "'




                  $products_id = $ayuda_producto['products_id'];
                  
                  
    $sumar_entregado_total_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.entregas_stock and a.admin_groups_id=6";
    $sumar_entregado_total_sales_query = tep_db_query($sumar_entregado_total_sales_raw);
    $sumar_entregado_total= tep_db_fetch_array($sumar_entregado_total_sales_query);

    $sumar_mercancia_entregado_procesando_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.status_entregas and a.admin_groups_id=6";
    $sumar_mercancia_entregado_procesando_sales_query = tep_db_query($sumar_mercancia_entregado_procesando_sales_raw);
    $sumar_mercancia_entregado_procesando= tep_db_fetch_array($sumar_mercancia_entregado_procesando_sales_query);

    $sumar_retirado_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $product_info['products_id'] . "'and o.orders_status =a.retirarado and a.admin_groups_id=6";
    $sumar_retirado_sales_query = tep_db_query($sumar_retirado_sales_raw);
    $sumar_retirado= tep_db_fetch_array($sumar_retirado_sales_query);

    $sumar_credito_total_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.credito and a.admin_groups_id=6";
    $sumar_credito_total_sales_query = tep_db_query($sumar_credito_total_sales_raw);
    $sumar_credito_total= tep_db_fetch_array($sumar_credito_total_sales_query);


    //$sumar_no_recogido_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $product_info['products_id'] . "'and o.orders_status =a.no_recogido and a.admin_groups_id=6";
    //$sumar_no_recogido_sales_query = tep_db_query($sumar_no_recogido_sales_raw);
    //$sumar_no_recogido= tep_db_fetch_array($sumar_no_recogido_sales_query);

    $sumar_pagado_total_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.pagado and a.admin_groups_id=6";
    $sumar_pagado_total_sales_query = tep_db_query($sumar_pagado_total_sales_raw);
    $sumar_pagado_total= tep_db_fetch_array($sumar_pagado_total_sales_query);

    $sumar_cobrados_total_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.cobrado and a.admin_groups_id=6";
    $sumar_cobrados_total_sales_query = tep_db_query($sumar_cobrados_total_sales_raw);
    $sumar_cobrados_total= tep_db_fetch_array($sumar_cobrados_total_sales_query);

    $sumar_pagado_transferencia_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.pagado_transferencia and a.admin_groups_id=6";
    $sumar_pagado_transferencia_sales_query = tep_db_query($sumar_pagado_transferencia_sales_raw);
    $sumar_pagado_transferencia= tep_db_fetch_array($sumar_pagado_transferencia_sales_query);

    $sumar_paypal_enviado_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.paypal_enviado and a.admin_groups_id=6";
    $sumar_paypal_enviado_sales_query = tep_db_query($sumar_paypal_enviado_sales_raw);
    $sumar_paypal_enviado= tep_db_fetch_array($sumar_paypal_enviado_sales_query);

    $sumar_pendiente_entrada_total_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status = a.pendiente_entrada and a.admin_groups_id=6";
    $sumar_pendiente_entrada_total_sales_query = tep_db_query($sumar_pendiente_entrada_total_sales_raw);
    $sumar_pendiente_entrada_total= tep_db_fetch_array($sumar_pendiente_entrada_total_sales_query);

    $sumar_credito_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.credito and a.admin_groups_id=6";
    $sumar_credito_sales_query = tep_db_query($sumar_credito_sales_raw);
    $sumar_credito= tep_db_fetch_array($sumar_credito_sales_query);


    $sumar_pagos_procesando_sales_raw = "select sum(products_quantity) as value, count(*) as products_quantity from " . TABLE_ORDERS_PRODUCTS . " op,  " . TABLE_ORDERS . " o, administrators a where o.orders_id = op.orders_id and op.products_id ='" . $products_id . "'and o.orders_status =a.status_liquidacion and a.admin_groups_id=6";
    $sumar_pagos_procesando_sales_query = tep_db_query($sumar_pagos_procesando_sales_raw);
    $sumar_pagos_procesando= tep_db_fetch_array($sumar_pagos_procesando_sales_query);


  
    $entradas_os = $sumar_entregado_total['value'];
    $salidas_os = $sumar_pagos_procesando['value'] + $sumar_credito['value'] + $sumar_retirado['value'] + $sumar_cobrados_total['value'] + $sumar_pagado_total['value'] + $sumar_pagado_transferencia['value'] + $sumar_paypal_enviado['value'];

  

   $resultado =  $entradas_os-$salidas_os;
     $pagado = $sumar_pagado_total['value'];


  
  
  
  
        $time1 = mktime(1, 1, 1, date("m"), date("d"), date("Y"));
      $oldday1 = date("Y-m-d", $time1);

             $sql_data_array = array('time_proveedores' => time()+rand(1,130000),
                                   'time_entradasysalidas' => $resultado,
                                   'time_ultimaactualizacion' => $oldday1,);

     tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '" . $ayuda_producto['products_id'] . "'");


  

                                                         //      echo $ayuda_producto['products_id'] . ' - ' . $resultado;



}  // fin actualizaciones


      if ($actualizar_cantidades){

     ?>
     


         <?php }
      }





                         if (ACTIVO_PRODUCTS_STATUS_VISUAL == 'True'){
                                    $sql_products_status =  'and  p.products_status = '. SELEC_PRODUCTS_STATUS_VISUAL .'';
                                                                      }
                                    $limit = LIMIT_ACTUALIZAR_CANTIDADES_VISUAL;
                                    $limit_stock = LIMIT_STOCK_ACTCAN_VISUAL;

                                                                                                                                                                   //p.stock_nivel = 6 and
         $ayuda_producto_values = tep_db_query("select * from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . 'products_stock' . " ps where p.products_id = ps.products_id and p.products_id = pd.products_id and ps.products_stock_real <= '" . $limit_stock . "' $sql_products_status order by ps.products_stock_real ASC LIMIT $limit");
    while ($ayuda_producto = tep_db_fetch_array($ayuda_producto_values)){
          $tr = $ayuda_producto['products_stock_real']-$ayuda_producto['products_stock_real']-$ayuda_producto['products_stock_real'];


         ?>
<p style="margin-top: 0; margin-bottom: 0"><a target="_blank" href="<?php echo 'edit_orders_tienda.php?action=edit&rr=&oID='.$ayuda_quanty['orders_id'] . '&codigobarras_negativos='.$ayuda_producto['products_id'] . '&unidades_negativos=' . $tr; ?>">Corregir</a> /
                                           <a target="_blank" href="<?php echo '/product_info.php?products_id='.$ayuda_producto['products_id']; ?>">Web</a> /
<?php echo $ayuda_producto['products_stock_real'] . ' / ' . $ayuda_producto['products_model'] . ' / ' . $ayuda_producto['products_name']; ?> </p>


    <?php
}



     ?>
       �<p>Productos que no tienen imagenes en la web, <a href="index.php?comprobar_images=ok">Actualizar Imagenes</a><a href="index.php?eliminar_toda_lista_images=ok"> Eliminar Lista Completa</a></p>
     <p>Importante, si quieres que alg�n producto no aparezca en esta lista solo
tienes que &quot;Inactive&quot; en el catalogo exel.</p>
      <?php

            $comprobar_images_print_values = tep_db_query("select * from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and comprobar_images = 1 order by p.products_model  DESC LIMIT 100");
    while ($comprobar_images_print = tep_db_fetch_array($comprobar_images_print_values)){




//   echo 'ok-';

        ?>


 <p style="margin-top: 0; margin-bottom: 0"><a href="<?php echo 'index.php?action=edit&rr=&oID='.$ayuda_quanty['orders_id'] . '&eliminar_producto_lista=ok&producto_id=' . $comprobar_images_print['products_id']; ?>">Eliminar</a> /
                                           <a target="_blank" href="<?php echo '/product_info.php?products_id='.$comprobar_images_print['products_id']; ?>">Web</a> /
                                           <a target="_blank" href="<?php echo 'categories.php?cPath=&pID='.$comprobar_images_print['products_id'].'&action=new_product'; ?>">EDITAR</a> /
<?php echo $ayuda_producto['time_entradasysalidas'] . ' / ' . $comprobar_images_print['products_image'] . ' / ' . $comprobar_images_print['products_model'] . '/ ' . $comprobar_images_print['products_name'] . ' / '; ?> </p>


         <?php

        }







            $comprobar_images = $_GET['comprobar_images'];

       if ($comprobar_images){
                                                                                                                                                                                               //or p.products_id = pd.products_id and p.comprobar_images = 1 and products_status = 0
          $comprobar_images_db_values = tep_db_query("select * from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and products_status = 1 or p.products_id = pd.products_id and p.comprobar_images = 1 and products_status = 0");
    while ($comprobar_images_db = tep_db_fetch_array($comprobar_images_db_values)){





                               // comprueba  si la imagen existe.
                   
    if(getimagesize(HTTP_SERVER . DIR_WS_CATALOG_IMAGES .$comprobar_images_db['products_image'])) {

                $sql_status_update_array = array('comprobar_images' => 0);
             tep_db_perform(TABLE_PRODUCTS, $sql_status_update_array, 'update', " products_id= '" . $comprobar_images_db['products_id'] . "' and products_status = 1");


 }else{

                    $sql_status_update_array = array('comprobar_images' => 1,);

             tep_db_perform(TABLE_PRODUCTS, $sql_status_update_array, 'update', " products_id= '" . $comprobar_images_db['products_id'] . "' and products_status = 1");


}


     if ($comprobar_images_db['products_image'] == 'no-foto.jpg'){

                   $sql_status_update_array = array('products_status' => 0,);
             tep_db_perform(TABLE_PRODUCTS, $sql_status_update_array, 'update', " products_id= '" . $comprobar_images_db['products_id'] . "'");

                  }else{


              }





}
} // comprobar imagenes

      if ($actualizar_cantidades){

     ?>

                           <script type="text/javascript">

    var pagina = '<?php echo 'index.php'; ?>';
    var segundos = 0;

    function redireccion() {

        document.location.href=pagina;

    }

    setTimeout("redireccion()",segundos);

     </script>

         <?php }


//eliminar todas la lista de imagenes.
    $eliminar_toda_lista_images = $_GET['eliminar_toda_lista_images'];
if ($eliminar_toda_lista_images){


                         $comprobar_images_db_values = tep_db_query("select * from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id");
    while ($comprobar_images_db = tep_db_fetch_array($comprobar_images_db_values)){

                   $sql_status_update_array = array('comprobar_images' => 0,);
             tep_db_perform(TABLE_PRODUCTS, $sql_status_update_array, 'update', " products_id= '" . $comprobar_images_db['products_id'] . "' and comprobar_images = 1");

                }

            ?>

                           <script type="text/javascript">

    var pagina = '<?php echo 'index.php'; ?>';
    var segundos = 0;

    function redireccion() {

        document.location.href=pagina;

    }

    setTimeout("redireccion()",segundos);

     </script>

         <?php

}


//eliminar todas la lista de imagenes.
    $eliminar_producto_lista = $_GET['eliminar_producto_lista'];
   $producto_id = $_GET['producto_id'];
if ($eliminar_producto_lista){


                   $sql_status_update_array = array('comprobar_images' => 0,);
             tep_db_perform(TABLE_PRODUCTS, $sql_status_update_array, 'update', " products_id= '" . $producto_id . "'");



            ?>

                           <script type="text/javascript">

    var pagina = '<?php echo 'index.php'; ?>';
    var segundos = 0;

    function redireccion() {

        document.location.href=pagina;

    }

    setTimeout("redireccion()",segundos);

     </script>
             </font></p>
         <?php

}





      } //ACTIVO CORREGIDOR

  }//admin




                                                    ?>
                                                    

                                                 <?php //if (ACTUALIZAR_TABLA_FABRICANTE = 'True'){ ?>
  <script language="javascript" src="actualizar_tabla_fabricantes.php"> </script></td>
                                                      <?php //} ?>





  <?php
   if ($login_groups_id == 9){
     ?>

                           <script type="text/javascript">

    var pagina = '<?php echo $_SERVER['REQUEST_URI']; ?>';
    var segundos = 600000;

    function redireccion() {

        document.location.href=pagina;

    }

    setTimeout("redireccion()",segundos);

     </script>

     <?php
}else{
    ?>



       
                           <script type="text/javascript">

    var pagina = '<?php echo $_SERVER['REQUEST_URI']; ?>';
    var segundos = 600000;

    function redireccion() {

        document.location.href=pagina;

    }

    setTimeout("redireccion()",segundos);

     </script>
     
     
     
     
     <?php
  }
       ?>
