<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

$lang['imglib_source_image_required'] = 'Vous devez spécifier une image source dans vos préférences.';
$lang['imglib_gd_required'] = 'La bibliothèque d’images GD est requise pour cette fonctionnalité.';
$lang['imglib_gd_required_for_props'] = 'Votre serveur doit prendre en charge la bibliothèque d’images GD afin de déterminer les propriétés de l’image.';
$lang['imglib_unsupported_imagecreate'] = 'Votre serveur ne prend pas en charge la fonction GD requise pour traiter ce type d’image.';
$lang['imglib_gif_not_supported'] = 'Les images GIF ne sont souvent pas prises en charge en raison de restrictions de licence. Vous devrez peut-être utiliser des images JPG ou PNG à la place.';
$lang['imglib_jpg_not_supported'] = 'Les images JPG ne sont pas prises en charge.';
$lang['imglib_png_not_supported'] = 'Les images PNG ne sont pas prises en charge.';
$lang['imglib_jpg_or_png_required'] = 'Le protocole de redimensionnement d’image spécifié dans vos préférences ne fonctionne qu’avec les types d’images JPEG ou PNG.';
$lang['imglib_copy_error'] = 'Une erreur s’est produite lors de la tentative de remplacement du fichier. Assurez-vous que votre répertoire de fichiers est accessible en écriture.';
$lang['imglib_rotate_unsupported'] = 'La rotation des images ne semble pas être prise en charge par votre serveur.';
$lang['imglib_libpath_invalid'] = 'Le chemin d’accès à votre bibliothèque d’images n’est pas correct. Veuillez définir le chemin correct dans vos préférences d’image.';
$lang['imglib_image_process_failed'] = 'Echec du traitement de l’image. Veuillez vérifier que votre serveur prend en charge le protocole choisi et que le chemin d’accès à votre bibliothèque d’images est correct.';
$lang['imglib_rotation_angle_required'] = 'Un angle de rotation est nécessaire pour faire pivoter l’image.';
$lang['imglib_invalid_path'] = 'Le chemin d’accès à l’image n’est pas correct.';
$lang['imglib_invalid_image'] = 'L’image fournie n’est pas valide.';
$lang['imglib_copy_failed'] = 'La routine de copie d’image a échoué.';
$lang['imglib_missing_font'] = 'Impossible de trouver une police à utiliser.';
$lang['imglib_save_failed'] = 'Impossible d’enregistrer l’image. Assurez-vous que l’image et le répertoire de fichiers sont accessibles en écriture.';
