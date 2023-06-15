<?php
namespace Learncom\Repositories;
use Learncom\Models\LearnCompany;
use Phalcon\Mvc\User\Component;

class Setcompany extends Component
{
    //create save
    public function saveCompany($newrecord)
    {
        if($newrecord->save()==true)
        {
            $result['success'] = true;
            $result['id'] = $newrecord->getSetId();
            $result['company_name'] = $newrecord->getSetCompanyName();
            $result['dir_type'] = $newrecord->getSetDirectorType();
            $result['dir_name'] = $newrecord->getSetDirectorName();
            $result['dir_passport'] = $newrecord->getSetDirectorPassport();
            $result['dir_country'] = $newrecord->getSetDirectorCountry();
            $result['share_type'] = $newrecord->getSetShareholderType();
            $result['share_name'] = $newrecord->getSetShareholderName();
            $result['share_passport'] = $newrecord->getSetShareholderPassport();
            $result['share_country'] = $newrecord->getSetShareholderCountry();
            $result['share_percent'] = $newrecord->getSetShareholderShare();
            $result['ben_name'] = $newrecord->getSetBeneficialName();
            $result['ben_country'] = $newrecord->getSetBeneficialCountry();
            $result['ben_passport'] = $newrecord->getSetBeneficialPassport();
            $result['ben_percent'] = $newrecord->getSetBeneficialPassport();
            $result['name'] = $newrecord->getSetContactName();
            $result['email'] = $newrecord->getSetContactEmail();
            $result['phone'] = $newrecord->getSetContactPhone();
            $result['skype'] = $newrecord->getSetContactSkype();
            $result['insert_time'] = $newrecord->getSetInsertTime();
        }
        else
        {
            $message =  "We can't store your info now: \n";
            foreach ($newrecord->getMessages() as $msg) {
                $message.=$msg."\n";
            }
            $result['success'] = false;
            $result['message'] = $message;
        }
        return $result;
    }
}



