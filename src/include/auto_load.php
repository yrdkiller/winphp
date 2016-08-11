<?php
function __autoload($classname) {
	$classpath = getClassPath();
	if (isset($classpath[$classname])) {
		include($classpath[$classname]);
	}
}
function getClassPath() {
    static $classpath=array();
    if (!empty($classpath)) return $classpath;
    if(function_exists("apc_fetch")) {
        $classpath = apc_fetch("fw:root:autoload:map:4789");
        if ($classpath) return $classpath;

        $classpath = getClassMapDef();
        apc_store("fw:root:autoload:map:4789",$classpath); 
    } else if(function_exists("eaccelerator_get")) {
        $classpath = eaccelerator_get("fw:root:autoload:map:4789");
        if ($classpath) return $classpath;

        $classpath = getClassMapDef();
        eaccelerator_put("fw:root:autoload:map:4789",$classpath); 
    } else {
        $classpath = getClassMapDef();
    }
    return $classpath;
}

function getClassMapDef() {
    return array(
			"BaseAction"			=> "/root/demo/src/application/controllers/BaseAction.php",
			"DemoController"			=> "/root/demo/src/application/controllers/DemoController.php",
			"BaseDao"			=> "/root/demo/src/application/models/dao/BaseDao.php",
			"DemoDao"			=> "/root/demo/src/application/models/dao/DemoDao.php",
			"Cache"			=> "/root/demo/src/application/models/integration/Cache.php",
			"FileCache"			=> "/root/demo/src/application/models/integration/Cache.php",
			"MemCachecc"			=> "/root/demo/src/application/models/integration/Cache.php",
			"CheckFrom"			=> "/root/demo/src/application/models/integration/CheckFrom.php",
			"Des"			=> "/root/demo/src/application/models/integration/Des.php",
			"Log"			=> "/root/demo/src/application/models/integration/Log.php",
			"Multi"			=> "/root/demo/src/application/models/integration/Multi.php",
			"Mydir"			=> "/root/demo/src/application/models/integration/Mydir.php",
			"ParseXml"			=> "/root/demo/src/application/models/integration/ParseXml.php",
			"Rsa"			=> "/root/demo/src/application/models/integration/Rsa.php",
			"Sign"			=> "/root/demo/src/application/models/integration/Sign.php",
			"User"			=> "/root/demo/src/application/models/integration/User.php",
			"Utility"			=> "/root/demo/src/application/models/integration/Utility.php",
			"BaseModel"			=> "/root/demo/src/application/models/service/BaseModel.php",
			"DemoModel"			=> "/root/demo/src/application/models/service/DemoModel.php",
			"SecretModel"			=> "/root/demo/src/application/models/service/SecretModel.php",
			"QFrameDB"			=> "/root/demo/src/include/db/QFrameDB.php",
			"QFrameDBPDO"			=> "/root/demo/src/include/db/QFrameDB.php",
			"QFrameDBStatment"			=> "/root/demo/src/include/db/QFrameDB.php",
			"QFrameDBException"			=> "/root/demo/src/include/db/QFrameDB.php",
			"QFrameDBExplainResult"			=> "/root/demo/src/include/db/QFrameDBExplainResult.php",

	);
}
?>