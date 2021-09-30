<?php
AddEventHandler("catalog", "OnSuccessCatalogImport1C", "AddCategoryPicture");
function AddCategoryPicture()
{
    // Путь до архива
    $sFilePathArc = $_SERVER["DOCUMENT_ROOT"] . "/upload/1c_catalog/groupImages.zip";
    // Путь до каталога, в который извлекаем файлы изображений
    $sFilePathDst = $_SERVER["DOCUMENT_ROOT"] . "/images/category";

    // Распаковываем архив
    $resArchiver = CBXArchive::GetArchive($sFilePathArc);
    $resArchiver->Unpack($sFilePathDst);

    // Получаем имена файлов в папке
    $dir = $_SERVER['DOCUMENT_ROOT'] . '/images/category';
    function myscandir($dir, $sort = 0)
    {
        $list = scandir($dir, $sort);

        if (!$list) {
            return false;
        }

        if ($sort == 0) {
            unset($list[0], $list[1]);
        } else {
            unset($list[count($list) - 1], $list[count($list) - 1]);
        }
        return $list;
    }

    // Переименовываем файлы, чтобы в имени был только внешний код категории
    $pictures = myscandir($dir);
    foreach ($pictures as $picture) {
        $name = explode("_", $picture);
        // Проверем расширение картинки, если JPG
        if (strpos($name[1], 'jpg')) {
            rename(
                $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $picture,
                $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.jpg'
            );
            // Получаем ID категории по внешнему коду
            $sections = CIBlockSection::GetList(
                Array("ID" => "ASC"),
                Array("IBLOCK_ID" => 20, "XML_ID" => $name[0]),
                false,
                Array('ID')
            );
            while ($ar_fields = $sections->GetNext()) {
                $bs = new CIBlockSection;
                $arFields = Array(
                    "PICTURE" => CFile::MakeFileArray(
                        $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.jpg'
                    ),
                    "DETAIL_PICTURE" => CFile::MakeFileArray(
                        $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.jpg'
                    ),
                );
                // Обновляем картинку категории
                $bs->Update($ar_fields['ID'], $arFields);
            }
            // Проверем расширение картинки, если PNG
        } elseif (strpos($name[1], 'png')) {
            rename(
                $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $picture,
                $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.png'
            );
            // Получаем ID категории по внешнему коду
            $sections = CIBlockSection::GetList(
                Array("ID" => "ASC"),
                Array("IBLOCK_ID" => 20, "XML_ID" => $name[0]),
                false,
                Array('ID')
            );
            while ($ar_fields = $sections->GetNext()) {
                $bs = new CIBlockSection;
                $arFields = Array(
                    "PICTURE" => CFile::MakeFileArray(
                        $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.png'
                    ),
                    "DETAIL_PICTURE" => CFile::MakeFileArray(
                        $_SERVER['DOCUMENT_ROOT'] . '/images/category/' . $name[0] . '.png'
                    ),
                );
                // Обновляем картинку категории
                $bs->Update($ar_fields['ID'], $arFields);
            }
        }
    }
    //Удаляем временную папку
    function my_delete_dir($mypath){
        $dir = opendir($mypath);
        while (($file = readdir($dir))){
            if (is_file($mypath."/".$file))
                unlink ($mypath."/".$file);
            elseif (is_dir($mypath."/".$file) && ($file != ".") && ($file != ".."))
                my_delete_dir ($mypath."/".$file);
        }
        closedir ($dir);
        rmdir ($mypath);
    }
    my_delete_dir($sFilePathDst);
}