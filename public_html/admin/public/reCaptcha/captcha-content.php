<script language="javascript">
function RefreshImage(valImageId) {
	var objImage = document.images[valImageId];
	if (objImage == undefined) {
		return;
	}
	var now = new Date();
	objImage.src = objImage.src.split('?')[0] + '?x=' + now.toUTCString();
}
</script>
<p style="width:80%;text-align:left">digite as letras como aparecem na imagem.</p>

<img src="admin/library/reCaptcha/get_captcha.php" id="imgCaptcha" width="140">
<input name="captchacode" type="text" id="captchacode" style="width:20%;" maxlength="9" >
<script type="text/javascript" > RefreshImage('imgCaptcha'); </script>
