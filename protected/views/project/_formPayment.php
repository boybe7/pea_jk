<h4>รายละเอียดค่าใช้จ่าย งวดที่ <?php echo $index;?></h4>
<hr style="margin-top:5px; ">
    
    
<script type="text/javascript">
  function bs_input_file() {
    $(".input-file").before(
      function() {
        if ( ! $(this).prev().hasClass('input-ghost') ) {
          var element = $("<input type='file' name='fileupload' id='fileupload' class='input-ghost' style='visibility:hidden; height:0'>");
          element.attr("name",$(this).attr("name"));
          element.change(function(){
            element.next(element).find('input').val((element.val()).split('\\').pop());
          });
          $(this).find("button.btn-choose").click(function(){
            element.click();
          });

          element.change(function(e) {
              filename = e.target.files[0].name;
              console.log(element)
          });
          
          $(this).find('input').css("cursor","pointer");
          $(this).find('input').mousedown(function() {
            $(this).parents('.input-file').prev().click();
            return false;
          });
          return element;
        }
      }
    );
  }

  

  $(function() {
    bs_input_file();

      var files;
      $('input[type=file]').on('change',prepareUpload);

      function prepareUpload(event) {
        files = event.target.files;
      }

      $("#form-import").on('submit',function(e){

        e.preventDefault();
        form = new FormData();
       
        form.append("vc_id", $('#vc_id').val());
        form.append('fileupload',files[0]);
        
         // $.ajax({
         //       type: "POST",
         //       url: "importBOQ",
         //       //dataType:"json",
         //       data: form,
         //       contentType: false,
         //       processData: false,
         //      success:function(response){
         //            $('#payment-content-1').html(response)
         //            //$('#form-import')[0].reset();
         //            //$("#boq-grid").yiiGridView("update",{});
                    
         //      }

         //     });

        // stop the form from submitting the normal way and refreshing the page
        event.preventDefault();
      });



  });


</script>

  <form method="POST" action="" id="form-import" enctype="multipart/form-data" class="pull-right">
    <div class="form-group">
      <div class="input-prepend input-file">
        <button class="btn btn-default btn-choose" type="button">Browse</button>
        <input type="text" name="filetext"  class="form-control" placeholder='Choose a file...' />
       
      </div>
      <button class="btn btn-success" id="importButton" type="submit" style="margin-top: -10px;"><i class="icon-excel icon-white"></i> Import</button>
      <?php
         $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'link',
            
            'type'=>'info',
            'label'=>'Submit',
            'icon'=>'plus white',
            'url'=>array('submitBOQ'),
            'htmlOptions'=>array('class'=>'pull-right','style'=>'margin-left: 10px'),
          )); 
      ?>
   
    </div>  
  </form>  


     <?php 
     echo '<input type="hidden" id="vc_id" name="vc_id" value="'.$model->id.'">';
     echo '<input type="hidden" id="index" name="index" value="'.$index.'">';
     echo '<div id="payment-content-'.$index.'" style="margin-top: -40px"> </div>';
     ?>


    
