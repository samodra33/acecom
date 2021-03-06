<?php

namespace App\Http\Controllers\DocumentNumber;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\DocumentNumber;
use App\Models\DocumentNumberSequence;

use Auth;
use App\User;

class DocumentNumberController extends Controller
{
    public function generate($documentType)
    {
        $documentNo = "";
        $documentSequence = 0;
        $nextSequence = 0;
        $prefix = "";
        $sequenceID = null;

        $getDocumentNumber = DocumentNumber::where("doc_no_type", $documentType)
        ->where("doc_no_status", 1)
        ->first();

        if(empty($getDocumentNumber)){
            return array();
        }

        $doc = $getDocumentNumber->doc_no_type;

        $getDocumentNumberSequence = DocumentNumberSequence::where("doc_no_id", $getDocumentNumber->doc_no_id)
        ->first();

        if(empty($getDocumentNumberSequence)){
            return array();
        }

        // Check for reset by month
        $checkRenewSequence = $this->renewSequence($getDocumentNumberSequence->sequence_id);
        if(isset($checkRenewSequence->sequence_id)){
                $getDocumentNumberSequence = $checkRenewSequence;
        }

        $sequenceID = $getDocumentNumberSequence->sequence_id;
        $prefix = $getDocumentNumber->doc_no_value;
        $doc = $getDocumentNumber->doc_no_type;
        $sequenceValue = intval($getDocumentNumberSequence->sequence_value);

        if( $sequenceValue < 99999){
            if($sequenceValue==0){
                $documentSequence = 1;
            }else{
                $documentSequence = $sequenceValue;
            }
        }

        $nextSequence = $documentSequence+1;

        $documentNo = $prefix.date("y").date("m").str_pad($documentSequence, 5, "0", STR_PAD_LEFT);

        $res = array(

            "sequence_id" => $sequenceID,
            "current_sequence" => $documentSequence,
            "next_sequence" => $nextSequence,
            "document_no" => $documentNo,
            "prefix" => $prefix

        );

        return $res;
    }

    public function updateSequence($sequenceId, $sequenceValue)
    {

        $getDocumentNumberSequence = DocumentNumberSequence::find($sequenceId);
        if(empty($getDocumentNumberSequence)){
            return array();
        }
        $lastSequenceValue = $getDocumentNumberSequence->sequence_value;

        $getDocumentNumberSequence->sequence_value = str_pad($sequenceValue, 5, "0", STR_PAD_LEFT);
        $getDocumentNumberSequence->save();

        return $this->isSequenceUpdated($sequenceId, $lastSequenceValue);
    }

    public function isSequenceUpdated($sequenceId, $lastSequenceValue)
    {
        $getDocumentNumberSequenceUpdate = DocumentNumberSequence::find($sequenceId);

        if(!empty($getDocumentNumberSequenceUpdate)){
            //check value after update and before update must be the same
            if( intval($getDocumentNumberSequenceUpdate->sequence_value ) == intval($lastSequenceValue) ){
                return array();
            }

            return $getDocumentNumberSequenceUpdate;
        }
        return array();
    }

    public function renewSequence($sequenceId)
    {

        $restartSequence = 0;

        $getDocumentNumberSequence = DocumentNumberSequence::find($sequenceId);
        if(empty($getDocumentNumberSequence)){
            return array();
        }
        $strSequenceLastMonth = date("Y-m", strtotime($getDocumentNumberSequence->sequence_last_update_dt));
        $strCurrentMonth = date("Y-m", strtotime(date("Y-m-d")));

        if($strSequenceLastMonth != $strCurrentMonth){
            $lastSequenceValue = $getDocumentNumberSequence->sequence_value;
            $getDocumentNumberSequence->sequence_value = str_pad($restartSequence, 5, "0", STR_PAD_LEFT);
            $getDocumentNumberSequence->sequence_last_update_by = 0;
            $getDocumentNumberSequence->sequence_last_update_dt = date("Y-m-d");
            $getDocumentNumberSequence->sequence_last_update_timestamp = now();
            $getDocumentNumberSequence->save();

            return $this->isSequenceUpdated($sequenceId, $lastSequenceValue);
        }

        return array(
            "renew_sequence" => "updated"
        );
    }

}
