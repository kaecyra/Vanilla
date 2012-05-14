<div class="SteamConnect">
   <div class="Message">Connecting to STEAM</div>
   <div class="Progress"></div>
</div>

<script type="text/javascript">
   jQuery(document).ready(function($){
      setTimeout(function(){
         window.location.href = '<?php echo $this->Data('SteamConnect'); ?>';
      }, 1000);
   });
</script>