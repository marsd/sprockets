<?php namespace Codesleeve\Sprockets\Parsers;

class ExtensionsParser extends ConfigParser
{
    /**
     * Returns all the extensions for this configuration
     * 
     * @return array
     */
    public function extensions($mime = null)
    {
        $extensions = array();
        $mime = $mime ?: $this->mime;
        $filters = array_keys($this->get('filters'));

        foreach ($filters as $filter)
        {
            if ($this->isValidExtension($filter, $mime)) {
                $extensions[] = $filter;
            }
        }

        return $extensions;
    }

    /**
     * Gets the extension for the current filename
     * 
     * @param  string $filename
     * @return string
     */
    public function extensionForFile($filename)
    {
        $newfile = $this->stripOffExtensions($filename);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        if ($newfile != $filename) {
            $extension = str_replace($newfile, '', $filename);
        }         

        return $extension;
    }

    /**
     * Returns all the mime types for the current mime
     * restriction.
     * 
     * @return bool
     */
    public function isValidExtension($extension, $mime)
    {
        $mimes = $this->get("mimes.$mime", '');
        $mimes = str_replace(' ', '', $mimes);
        $mimes = explode(',', $mimes);

        return is_null($mime) || in_array($extension, $mimes);
    }

    /**
     * Returns true or false if this has a valid extension
     * 
     * @param  string  $fullpath
     * @param  string  $mime     
     * @return boolean           
     */
    public function hasValidExtension($fullpath, $mime)
    {
        foreach ($this->extensions($mime) as $extension)
        {
            $strip = $this->stripFromEnding($extension, $fullpath);
            if ($strip != $fullpath) {
                return true;
            }
        }

        return false;
    }

    /**
     * Take off all dots from end of filename.
     * 
     * @param  [type] $filename [description]
     * @return [type]           [description]
     */
    public function stripOffExtensions($filename)
    {
        foreach ($this->extensions() as $extension)
        {
            $strip = $this->stripFromEnding($extension, $filename);
            if ($strip != $filename) {
                return $strip;
            }
        }

        return $filename;
    }

    /**
     * Returns the mime type if known
     * 
     * @param  string $filename
     * @return string  
     */
    public function mimeType($filename)
    {
        $extension = $this->extensionForFile($filename);
        $mimesets = $this->get("mimes", array());

        foreach ($mimesets as $mimetype => $mimeset)
        {
            $mimes = explode(',', str_replace(' ', '', $mimeset));
            if (in_array($extension, $mimes)) {
                return $mimetype;
            }
        }

        return null;
    }
}