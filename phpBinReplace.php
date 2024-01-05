<?php

class phpBinReplace
{
    private $InputFile;
    private $OutputFile;
    private $ReplaceList;
    public function __construct($InputFile = "", $ReplaceList = [])
    {
        $this->InputFile = $InputFile;
        $this->SetReplaceList($ReplaceList);
        $this->SetOutputFile($InputFile);
    }
    // Input File GET|SET
    function SetInputFile($input_file)
    {
        $this->InputFile = $input_file;
    }
    function GetInputFile(): string
    {
        return $this->InputFile;
    }
    // Output File GET|SET
    public function SetOutputFile($output_file = '')
    {
        if (strlen($output_file) < 3) {
            $pos = strrpos($this->InputFile, '.');
            if ($pos < 2)
                return;
            $this->OutputFile = substr($this->InputFile, 0, $pos) . '_new' . substr($this->InputFile, $pos);
        } else
            $this->OutputFile = $output_file;
    }
    public function GetOutputFile(): string
    {
        return $this->OutputFile;
    }
    // Replace List GET|SET
    public function SetReplaceList($replace_list, $sym = '0')
    {
        if(strlen($sym) > 1) $sym = $sym[0];
        $this->ReplaceList = [];
        foreach ($replace_list as $k => $v) {
            if (gettype($k) == 'integer') $this->ReplaceList[$v] = str_repeat($sym, strlen($v));
            elseif (strlen($v) > strlen($k)) $this->ReplaceList[$k] = substr($v, 0, strlen($k));
            elseif (strlen($v) < strlen($k)) $this->ReplaceList[$k] = $v . str_repeat($sym, strlen($k) - strlen($v));
            else $this->ReplaceList[$k] = $v;
        }
    }
    public function GetReplaceList(): array
    {
        return $this->ReplaceList;
    }
    // REPLACE
    function Build()
    {
        if(!is_file($this->InputFile)) return false;
        if(strlen($this->OutputFile) < 5) $this->SetOutputFile();
        // Read
        $ibin = '';
        $hibin = fopen($this->InputFile, "r");
        while (!feof($hibin))
            $ibin .= bin2hex(fread($hibin, 1));
        fclose($hibin);
        // Replace
        $ibin = str_ireplace(array_keys($this->ReplaceList), array_values($this->ReplaceList), $ibin);
        // Save
        $hobin = fopen($this->OutputFile, "w");
        for ($i = 0; $i < strlen($ibin); $i += 2) {
            if (!isset($ibin[$i + 1]))
                break;
            fwrite($hobin, hex2bin($ibin[$i] . $ibin[$i + 1]), 1);
        }
        fclose($hobin);
        return $this->OutputFile;
    }
}