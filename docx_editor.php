<?php

$input = 'resources/test.docx';
$output = 'modified.docx';
$replacements = [
    'name' => 'test successful 1',
    'email' => 'test successful 2',
    'address' => 'test successful 3',
    'time' => time(),
    'date'=>date('d-m-y')
];

$successful = searchReplaceWordDocument($input, $output, $replacements);

echo $successful ? "Successfully created $output" : 'Failed!';

/**
 * In this example we're replacing some token strings.  Using
 *
 * @param string $input
 * @param string $output
 * @param array  $replacements
 *
 * @return bool
 */
function searchReplaceWordDocument(string $input, string $output, array $replacements): bool
{
    if (copy($input, $output)) {
        $zip = new ZipArchive();

        if ($zip->open($output, ZipArchive::CREATE) !== true) {
            return false;
        }
        $header = $zip->getFromName('word/header1.xml');
        $body   = $zip->getFromName('word/document.xml');
        $footer = $zip->getFromName('word/footer1.xml');

        $replacements = addTemplateFormate($replacements);

        // Replace
        $header = str_replace(array_keys($replacements), array_values($replacements), $header);
        $body = str_replace(array_keys($replacements), array_values($replacements), $body);
        $footer = str_replace(array_keys($replacements), array_values($replacements), $footer);
        

        // Write back to the document and close the object
        if (false === $zip->addFromString('word/header1.xml', $header)) {
            return false;
        }
        if (false === $zip->addFromString('word/document.xml', $body)) {
            return false;
        }

        if (false === $zip->addFromString('word/footer1.xml', $footer)) {
            return false;
        }

        $zip->close();

        return true;
    }

    return false;
}


/**
 * Formate template placeholder
 * @param array $data data to be replace
 * @return array formated data
 */

function addTemplateFormate(array $data): array
{
    $formatedArray = array_combine(
        array_map(function ($key) {
            return '{'.$key.'}';
        }, array_keys($data)),
        $data
    );
    return $formatedArray;
}
