<?php
	$templateFolder = 'templates/';
	$pagesFolder    = 'pages/';

	$pagesScaned = scanFolder($pagesFolder);
	$requestedPageName  = getStringFrom($_SERVER['REQUEST_URI'], getNameOfThisFile());
	if ($requestedPageName == 'export')
	{}
	else if ($requestedPageName)
	{
		$templatePage  = file_get_contents($templateFolder.'base.html', FILE_USE_INCLUDE_PATH);
		$pageContent   = file_get_contents($pagesFolder.$requestedPageName, FILE_USE_INCLUDE_PATH);

		$bodyPage      = getStringBetween($pageContent, '[body]', '[/body]');
		$titlePage     = getStringBetween($pageContent, '[title]', '[/title]');
		$headerName    = getStringBetween($pageContent, '[header]', '[/header]');
		$footerName    = getStringBetween($pageContent, '[footer]', '[/footer]');

		$headerContent = '';
		$footerContent = '';

		if ($headerName && file_exists($templateFolder.$headerName.'.html'))
		{
			$headerContent = file_get_contents($templateFolder.$headerName.'.html', FILE_USE_INCLUDE_PATH);
		}

		if ($footerName && file_exists($templateFolder.$footerName.'.html'))
		{
			$footerContent = file_get_contents($templateFolder.'footer.html', FILE_USE_INCLUDE_PATH);
		}

		$renderedPage = $templatePage;
		$renderedPage = str_replace('[body]', $bodyPage, $renderedPage);
		$renderedPage = str_replace('[title]', $titlePage, $renderedPage);
		$renderedPage = str_replace('[header]', $headerContent, $renderedPage);
		$renderedPage = str_replace('[footer]', $footerContent, $renderedPage);
		$renderedPage = str_replace('[project]', split('/'.getNameOfThisFile(), $_SERVER['SCRIPT_NAME'])[0], $renderedPage);
		echo $renderedPage;
	}
	else
	{
?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta charset="utf-8">
			<title>Prototyper Web</title>
			<style type="text/css">
				*
				{
					font-family: "Comic Sans MS", cursive, sans-serif;
				}

				body
				{
					background-color: #564342;
					padding: 0;
					margin: 0;
				}

				.container
				{
					background-color: #f7f7f7;
					margin: 0px 150px;
					padding: 50px;
					padding-top: 0px;
					border: 1px solid #543734;
					border-bottom-left-radius: 15px;
					border-bottom-right-radius: 15px;
    				box-shadow: -10px 10px 15px #3e22056e, 10px 10px 15px #3e22056e;
				}

				.title
				{
					margin-top: 30px;
					color: #006a7d;
					font-size: 40px;
					text-align: center;
				}

				.subtitle
				{
					margin-top: 20px;
					color: #006a7d;
				}

				.text-helper
				{
					color: #909090;
					font-size: 13px;
				}

				.actions
				{
					display: block;
					margin-top: 40px;
					padding: 3px 0px;
				}

				.btn-link
				{
					text-decoration: none;
					background-color: #2cc0e3;
					color: white;
					display: inline-block;
					text-align: center;
					vertical-align: middle;
					cursor: pointer;
					border: 1px solid #28aece;
					padding: 8px 8px;
					font-size: 14px;
					border-radius: 5px;
				}

				.btn-link:focus, .btn-link:hover
				{
					background-color: #ccd523;
					border: 1px solid #a8af20;
				}

				.page-link
				{
					text-decoration: none;
					color: #006a7d;
				}

				.page-link:focus, .page-link:hover
				{
					color: #ccd523;
				}

				.page-folder
				{
					color: #006a7d;
				}
			</style>
		</head>
		<body>
			<div class="container">
				<div class="title">Bienvenido a Prototyper Web</div>
				<div class="actions">
					<!-- <a href="<?php /*getNameOfThisFile()*/ ?> /export" class="btn-link pull-right">Exportar</a> -->
					<a href="" class="btn-link pull-right">Recargar</a>
				</div>
				<?php if ($pagesScaned) { ?>
					<div class="subtitle">
						Actualmente usted dispone de las siguientes páginas para navegar
					</div>
					<div class="text-helper">
						Presione una de la lista para comenzar con la navegación.
					</div>
					<?php showPagesScaned($pagesScaned); ?>
				<?php } else { ?>
					<div class="subtitle">
						Actualmente usted no dispone de páginas para ver
					</div>
				<?php } ?>
			</div>
		</body>
	</html>
<?php } ?>



<?php
	// =======================================================================================================================
	//                                                        Functions                                                       
	// =======================================================================================================================


	/**
	 * Extrae de una cadena de texto un segmento que se encuentre entre otros dos textos.
	 *
	 * @param      string  $str    Texto del cual se va a extraer
	 * @param      string  $start  Texto que marca el inicio de la búsqueda
	 * @param      string  $end    Texto que marca el final de la búsqueda
	 *
	 * @return     string  Texto encontrado.
	 */
	function getStringBetween($str, $start, $end)
	{
		$str = ' '.$str;
		$ini = strpos($str, $start);

		if (!$ini)
			return '';

		$ini += strlen($start);
		$len = strpos($str, $end, $ini) - $ini;
		return substr($str, $ini, $len);
	}

	/**
	 * Recorta una cadena de texto a partir de otro texto.
	 *
	 * @param      string  $str    Texto que se va a recortar
	 * @param      string  $start  Texto que marca el inicio del corte
	 *
	 * @return     string  Texto recordado.
	 */
	function getStringFrom($str, $start)
	{
		$str = ' '.$str;
		$ini = strpos($str, $start);

		if (!$ini)
			return '';
		$ini += strlen($start);
		return substr($str, $ini);
	}

	/**
	 * Revisa recursivamente el contenido de la carpeta seleccionada,
	 *
	 * @param      string  $dir    Nombre de la carpeta a revisar,
	 * @return     array   Arreglo con todos los archivos y carpetas encontrados
	 */
	function scanFolder($dir)
	{
		$actDir = scandir($dir);
		$dirs = [];
		foreach ($actDir as $d)
		{
			if (is_file($dir.$d) && strpos($d, '.html'))
			{
				$dirs[] = $d;
			}
			else if (is_dir($dir.$d) && $d != '..' && $d != '.')
			{
				$dirs[$d] = scanFolder($dir.$d.'/');
			}
		}
		return $dirs;
	}

	/**
	 * Genera en pantalla el listado de carpetas y archivos, es recursivo.
	 *
	 * @param      <type>  $ps      Arreglo que contiene todos los archivos y carpetas.
	 * @param      string  $parent  Nombre de las carpetas provinientes.
	 */
	function showPagesScaned($ps, $parent = '')
	{
		echo '<ul>';
		if (is_array($ps))
		{
			foreach ($ps as $folder => $page)
			{
				if (is_array($page))
				{
					echo '<li>';
					echo '<strong class="page-folder">'.$folder.':</strong>';
					showPagesScaned($page, $parent.$folder.'/');
					echo '</li>';
				}
				else
				{
					echo '<li>';
					echo'<a class="page-link" href="'.getNameOfThisFile().'/';
					echo ($parent) ? $parent : '';
					echo $page.'">'.$page.'</a>';
					echo '</li>';
				}
			}
		}
		echo '</ul>';
	}

	/**
	 * Busca el nombre del archivo actual del código.
	 *
	 * @return     string  El nombre encontrado.
	 */
	function getNameOfThisFile()
	{
		return basename(__FILE__);
	}
?>