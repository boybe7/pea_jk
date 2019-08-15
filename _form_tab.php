<script type="text/javascript">
    $('#tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    
    $('a[data-toggle="tab"]').on('shown', function (e) {
        e.target // activated tab
        e.relatedTarget // previous tab
    });
</script>

<ul class="nav nav-tabs">
  
   <?php
      if(empty($model->id))
      {
        echo '<li class="active"><a href="#projTab" data-toggle="tab">โครงการ</a></li>';
        echo '<li ><a href="#boqTab" data-toggle="tab">BOQ</a></li>';
      }
      else{
        echo '<li ><a href="#projTab" data-toggle="tab">โครงการ</a></li>';
        echo '<li class="active"><a href="#boqTab" data-toggle="tab">BOQ</a></li>';
      }

   ?>
</ul>
<div class="tab-content   well-tab">
    <!------  Project Tab ------------>
    <div class="tab-pane active" id="projTab">
	<h4>รายละเอียดโครงการ</h4>
	<hr style="margin-top:5px; ">

	
		
    </div> <!-- end tab-pan -->


    <!------  BOQ Tab ------------>
    <div class="tab-pane" id="boqTab">
    <h4>รายละเอียดโครงการ2 </h4>
    <hr style="margin-top:5px; ">

        
    </div> <!-- end tab-pan -->
	
</div>	

