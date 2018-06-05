<?php

namespace Drupal\twig_extension_theme_include;

class Theme_Include_Twig_Extension extends \Twig_Extension {

	public function getName() {
		return 'twig_extension_theme_include.theme_include_twig_extension';
	}

	public function getFunctions() {
		return array(
			// Create a custom twig function with all the options the default twig include requires.
			new \Twig_SimpleFunction('theme_include', [$this, 'theme_include'], array('needs_environment' => true, 'needs_context' => true, 'is_safe' => array('all')))
		);
	}

	public function file_exists_in_theme($file_path, $theme) {
		// Get the path to the theme.
		$theme_path = $theme->getPath();
		// Build the path to the file in the theme.
		$theme_file_path = $theme_path . '/' . $file_path;
		// If the file exists in the theme.
		if(file_exists($theme_file_path)) {
			// Return the path to the file in the theme.
			return $theme_file_path;
		}
		// If the file does not exist in the theme.
		else {
			// Return false.
			return false;
		}
	}

	public function theme_include(\Twig_Environment $env, $context, $template, $variables = array(), $withContext = true, $ignoreMissing = false, $sandboxed = false) {

		// Get the active theme.
		$active_theme = \Drupal::service('theme.manager')->getActiveTheme();
		// If the file exists in the active theme.
		if($active_theme_file_path = $this->file_exists_in_theme($template, $active_theme)) {
			// Call Twig include with the active theme file path.
			return twig_include($env, $context, $active_theme_file_path, $variables, $withContext, $ignoreMissing, $sandboxed);
		}

		// Set a variable to hold the current theme as we loop through. Set initially to the active theme.
		$loop_theme = $active_theme;
		// While the current loop theme has a base theme.
		while($loop_theme->getBaseThemes()) {
			// Set the loop theme to the base theme.
			$loop_theme = array_shift(array_values($loop_theme->getBaseThemes()));
			// If the file exists in the loop theme.
			if($loop_theme_file_path = $this->file_exists_in_theme($template, $loop_theme)) {
				// Call Twig include with the loop theme file path.
				return twig_include($env, $context, $loop_theme_file_path, $variables, $withContext, $ignoreMissing, $sandboxed);
			}
		}

		// Generate a path to the file in the active theme.
		$active_theme_path = $active_theme->getPath() . '/' . $template;
		// No active theme has the file, but we pass the path into the twig include as if the file existed in the active theme directory (let the core twig function handle it).
		return twig_include($env, $context, $active_theme_path, $variables, $withContext, $ignoreMissing, $sandboxed);

	}

}