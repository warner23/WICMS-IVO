`<?php

function autoload($classes, $deps_file)
{
$deps =array(); //Stores the classes that have been scanned
$stack =array(); //Stores the classes about to be scanned, needed to avoid scanning twice the same class

foreach($classes as $class)
			load_class_file($class, $deps, $stack);

	$stream	="<?php".PHP_EOL;

foreach($deps as $dep)
{
	$stream	.=str_replace("\\", "/", "require(\"".$dep.".php\");".PHP_EOL);
	$stream	.="use ".$dep.";".PHP_EOL;
}

	$stream	.="?>".PHP_EOL;

			file_put_contents($deps_file, $stream);
}

//==================================================

function load_class_file($class_path, &$deps, &$stack)
{
if(!in_array($class_path, $stack))
{
$stack[] =$class_path;
$file =$class_path.".php";

	if(file_exists($file))
	{
					$content	=file_get_contents($file);
					$lines		=explode("\n", $content);
					$namespace;
					$uses		=array();

		foreach($lines as $line)
		{
			if(substr($line, 0, strlen("namespace ")) == "namespace ")
			{
					$namespace	=str_replace(";", "", str_replace("namespace ", "", $line));
			}
			if(substr($line, 0, strlen("use ")) == "use ")
			{
					$uses[]		=substr($line, strlen("use "), strlen($line) - strlen("use ") - 1);

								load_class_file(end($uses), $deps, $stack);
			}
			else if(strpos($line, "extends") > 0)
			{
					$line		=str_replace("class ", "", $line);
					$line		=str_replace(" extends ", ";", $line);
					$fields		=explode(";", $line);
					
					$child		=$fields[0];
					$mother		=$fields[1];

					$found		=false;

				if(file_exists($namespace."\\".$mother.".php"))
							load_class_file($namespace."\\".$mother, $deps, $stack);
			}
			else if(strpos($line, "new ") > 0)
			{
					$data		=explode("new ", $line)[1];
					$class		=trim(explode("(", $data)[0]);

					$found		=false;

				foreach($uses as $use)
				if(strpos($use, $class))
				{
					$found		=true;
								break;
				}

				if(!$found)
				if(file_exists($namespace."\\".$class.".php"))
								load_class_file($namespace."\\".$class, $deps, $stack);
			}
			else if(strpos($line, "::") > 0)
			{
					$data		=explode("::", $line)[0];
					$data		=explode(" ", $data);
					$class		=trim(end($data));

					$found		=false;

				foreach($uses as $use)
				if(strpos($use, $class))
				{
					$found		=true;
								break;
				}

				if(!$found)
				if(file_exists($namespace."\\".$class.".php"))
								load_class_file($namespace."\\".$class, $deps, $stack);
			}
		}

					$deps[]		=$class_path;
	}
}
}

?>