function login() {
	var p = hex_sha1(document.getElementById("pass").value);
	var k = document.getElementById("key").value;
	var h = hex_sha1(k+p);
	var u = document.getElementById("user").value;
	loadAction("?a=login&h="+h+"&u="+u, "login_status", true);
}