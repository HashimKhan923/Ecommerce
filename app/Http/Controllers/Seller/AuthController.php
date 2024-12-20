<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
// use App\Models\SellerInfromation;
use App\Models\Shop;
use App\Models\BusinessInformation;
use App\Models\SellingPlatforms;
use App\Models\SocialPlatforms;
use App\Models\BankDetail;
use App\Models\CreditCard;
use Hash;
use Mail;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Notification;

class AuthController extends Controller
{
    public function register (Request $request) {
    try 
    {
            $check = User::where('email',$request->email)->first();

                if($check == null)
                {
                    $new = new User();
                
                    $new->email = $request->email;

                }
                else
                {
                    $new = User::where('email',$request->email)->where('user_type','customer')->first();

                    if(!$new)
                    {
                        $response = ['status'=>false,"message" => "This email is already registered as a seller please try diffrent email!"];
                        return response($response, 422);
                    }
                }
                
                $new->name = $request->name; 
                $new->address = $request->address;
                $new->city = $request->city;
                $new->state = $request->state;
                $new->country = $request->country;
                $new->postal_code = $request->postal_code;
                $new->phone = $request->phone;
                
                $token = uniqid();
                $new->remember_token = $token;
                $new->password = Hash::make($request->password);
                $new->user_type = 'seller';
                $new->is_active = 1;
                $new->platform = $request->platform;
                $new->device_name = $request->device_name;
                $new->device_type = $request->device_type;
                $new->location = $request->location;
                $new->save();
                

                $shop = new Shop();
                $shop->seller_id = $new->id;
                $shop->name = $request->shop_name;
                $shop->address = $request->shop_address;
                if($request->file('logo')){

                    $file= $request->logo;
                    $filename= date('YmdHis').$file->getClientOriginalName();
                    $file->move(public_path('ShopLogo'),$filename);
                    $shop->logo = $filename;
                }

                if($request->file('banner')){

                    $file= $request->banner;
                    $filename= date('YmdHis').$file->getClientOriginalName();
                    $file->move(public_path('ShopBanner'),$filename);
                    $shop->banner = $filename;
                }
                $shop->save();


                // $BusineesInformation = new BusinessInformation();
                // $BusineesInformation->seller_id = $new->id;
                // $BusineesInformation->first_name = $request->business_first_name;
                // $BusineesInformation->last_name = $request->business_last_name;
                // $BusineesInformation->business_name = $request->business_name;
                // $BusineesInformation->ein_number = $request->ein_number;
                // $BusineesInformation->address1 = $request->address1;
                // $BusineesInformation->address2 = $request->address2;
                // $BusineesInformation->zip_code = $request->business_zip_code;
                // $BusineesInformation->country = $request->business_country;
                // $BusineesInformation->state = $request->business_state;
                // $BusineesInformation->city = $request->business_city;
                // $BusineesInformation->phone_number = $request->business_phone_number;
                // $BusineesInformation->business_email = $request->business_email;
                // $BusineesInformation->save();


                if($request->selling_platforms)
                {
                    foreach($request->selling_platforms as $items)
                    {
                    $SellingPlatforms = new SellingPlatforms();
                    $SellingPlatforms->seller_id = $new->id;
                    $SellingPlatforms->name = $items['selling_platform_name'];
                    $SellingPlatforms->link = $items['selling_platform_link'];
                    $SellingPlatforms->save();
                    }
                }    

                if($request->social_platforms)
                {
                    foreach($request->social_platforms as $items)
                    {
                    $SocialPlatforms = new SocialPlatforms();
                    $SocialPlatforms->seller_id = $new->id;
                    $SocialPlatforms->name = $items['social_platform_name'];
                    $SocialPlatforms->link = $items['social_platform_link'];
                    $SocialPlatforms->save();
                    }
                }    

                // $BankDetail = new BankDetail();
                // $BankDetail->seller_id = $new->id;
                // $BankDetail->business_name = $BusineesInformation->business_name;
                // $BankDetail->bank_name = $request->bank_name;
                // $BankDetail->account_title = $request->account_title;
                // $BankDetail->routing_number = $request->routing_number;
                // $BankDetail->account_number = $request->account_number;
                // $BankDetail->save();
                

                // $CreditCard = new CreditCard();
                // $CreditCard->seller_id = $new->id;
                // $CreditCard->name_on_card = $request->name_on_card;
                // $CreditCard->cc_number = $request->cc_number;
                // $CreditCard->exp_date = $request->exp_date;
                // $CreditCard->cvv = $request->cvv;
                // $CreditCard->zip_code = $request->card_zip_code;
                // $CreditCard->save();

                // Stripe::setApiKey(config('services.stripe.secret'));

            
                    
                //     $account = Account::create([
                //         'type' => 'custom', 
                //         'country' => $request->business_country, 
                //         'email' => $request->email,
                //         'business_type' => 'individual',
                //         'capabilities' => [
                //             'card_payments' => ['requested' => true],
                //             'bank_transfer_payments' => ['requested' => true],
                //             'transfers' => ['requested' => true],
                //         ],
                //         'tos_acceptance' => [
                //             'date' => strtotime(now()),
                //             'ip' => $request->ip(),
                //         ],
                //         'business_profile' => [
                //             'name' => $request->shop_name,
                //             // replace the request name with shop->id
                //             'url' => 'https://dragonautomart.com/store/' . $shop->id,
                //             'mcc' => '5533',
                //         ],
                //         'settings' => [
                //             'payouts' => [
                //                 'statement_descriptor'=> $request->shop_name
                //             ],
                //             'payments' => [
                //                 'statement_descriptor'=> $request->shop_name
                //             ]
                            
                //         ],
                //         'company' => [
                //             'name' => 'Dragonautomart LLC',
                //             'tax_id' => '933028427', 
                //         ],
                //         'individual' => [
                //             'id_number' => $request->ssn,
                //         //'id_number' => '933-02-8427',
                //             'first_name' => $request->business_first_name,
                //             'last_name' => $request->business_last_name,
                //             'email' => $request->business_email,
                //             'phone' => $request->business_phone_number,
                //             'dob' => [
                //                 'day' => $request->business_date,
                //                 'month' => $request->business_month,
                //                 'year' => $request->business_year,
                //             ],
                //             'ssn_last_4' => $request->last_4_ssn,
                //             'address' => [
                //                 'line1' => $request->address1,
                //             'line2' => $request->address2,
                //                 'city' => $request->business_city,
                //                 'state' => $request->business_state,
                //                 'postal_code' => $request->business_zip_code,
                //                 'country' => $request->business_country,

                //             ],
                //         ],
                //     ]);

                //     $bankAccount = $account->external_accounts->create([
                //         'external_account' => [
                //             'object' => 'bank_account',
                //             'country' => $request->business_country,
                //             'currency' => 'usd',
                //             'account_holder_name' => $request->account_title,
                //             'account_holder_type' => 'individual',
                //             'routing_number' => $request->routing_number, 
                //             'account_number' => $request->account_number,
                //         ],
                //     ]);

                    

                //     User::where('id', $new->id)->update([
                //         'stripe_account_id' => $account->id,
                //         'bank_account_id' => $bankAccount->id
                //     ]);

                    //  return response()->json(['success' => true, 'account_id' => $account->id]);


                    Mail::send(
                        'email.seller_email_verification',
                        [
                            'token'=>$token,
                            'name'=>$request->name,
                        ], 
                    
                    function ($message) use ($request) {
                        $message->from('support@dragonautomart.com','Dragon Auto Mart');
                        $message->to($request->email);
                        $message->subject('Email Verification');
                    });
            
                    if($check == null)
                    {
                        Notification::create([
                            'notification' => 'New Seller Registered Successfully!'
                        ]);
            
                        $response = ['status'=>true,"message" => "we have send the verification email to your provided email, please verify your email so that you may login to your dashboard."];
                        return response($response, 200);
                    }
                    else
                    {
                        $response = ['status'=>true,"message" => "your registration as a seller successfully completed!"];
                        return response($response, 200);
                    }
        }
        catch (\Exception $e)
        {
            


            // Mail::send(
            //     'email.exception',
            //     [
            //         'exceptionMessage' => $e->getMessage(),
            //         'exceptionFile' => $e->getFile(),
            //         'exceptionLine' => $e->getLine(),
            //     ],
            //     function ($message) {
            //         $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
            //         $message->to('support@dragonautomart.com'); // Send to support email
            //         $message->subject('Dragon Exception');
            //     }
            // );
            if($account->id)
            {
                $account = Account::retrieve($account->id);
                $account->delete();
            }


            if($new)
            {
                User::find($new->id)->delete();
            }    

            $response = ['status'=>false,"message" => $e->getMessage()];
            return response($response, 422);
        }


        


        
        

    }

    public function login(Request $request) {

        try
        {

            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:6',
            ]);
            if ($validator->fails())
            {
                return response(['errors'=>$validator->errors()->all()], 422);
            }
            
            $user = User::with('shop','seller_information')->where('email', $request->email)->first();
            if ($user) {
    
             if($user->remember_token == null)
             {
                if($user->is_active == 1)
                {
                    if (Hash::check($request->password, $user->password)) {
        
                        if($user->user_type == 'seller')
                        {
                            if($user->is_verify == 1)
                            {
                                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                                $response = ['status'=>true,"message" => "Login Successfully",'token' => $token,'user'=>$user];
                                return response($response, 200);
                            }
                            else
                            {
                                $response = ['status'=>false,"message" => "Your account is not verify by admin"];
                                return response($response, 422);
                            }   
    
                        }
                        else
                        {
                            $response = ['status'=>false,"message" => "You are not a seller"];
                            return response($response, 422);
                        }
        
        
                        
                    } else {
                        $response = ['status'=>false,"message" => "Password mismatch"];
                        return response($response, 422);
                    }
        
                }
                else
                {
                    $response = ['status'=>false,"message" =>'Your Account has been Blocked by Admin!'];
                    return response($response, 422);
                }
             } 
             else
             {
                // $token = uniqid();
                // $user->remember_token = $token;
                // $user->save();
    
                // Mail::send(
                //     'email.seller_verification',
                //     [
                //         'token'=>$token,
                //         'name'=>$user->name,
                //         //'last_name'=>$query->last_name
                //     ], 
                
                // function ($message) use ($user) {
                //     $message->from(env('MAIL_USERNAME'));
                //     $message->to($user->email);
                //     $message->subject('Email Verification');
                // });
    
    
                $response = ['status'=>false,"message" =>'your email is not verified. we have sent a verification link to your email while registration!'];
                return response($response, 422);
             }
    
    
            } else {
                $response = ['status'=>false,"message" =>'User does not exist'];
                return response($response, 422);
            }

        }
        catch (\Exception $e) {
            


            Mail::send(
                'email.exception',
                [
                    'exceptionMessage' => $e->getMessage(),
                    'exceptionFile' => $e->getFile(),
                    'exceptionLine' => $e->getLine(),
                ],
                function ($message) {
                    $message->from('support@dragonautomart.com', 'Dragon Auto Mart');
                    $message->to('support@dragonautomart.com'); // Send to support email
                    $message->subject('Dragon Exception');
                }
            );

            return response()->json(['status' => 422, 'message' => $e->getMessage()]);
        }
        
    }

    public function profile_view($id)
    {
      $data = User::with('seller_information','SellingPlatforms','SocialPlatforms','BankDetail','CreditCard',)->where('id',$id)->first();

      return response()->json(['data'=>$data],200);
    }

        public function usercheck(Request $request)
        {
            $user=auth('api')->user();
            return response()->json(['admin_profile'=>$user],200);
        }

    public function profile_update(Request $request){
        $id=$request->id;
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required|string|max:255',
        //     'email' => "required|email|max:255|unique:users,email,$id,id",
        //     'phone'=>'required|min:10|max:15',
        //     //'password' => 'required|string|min:6|confirmed'n,
        // ]);
        // if ($validator->fails())
        // {
        //     return response(['errors'=>$validator->errors()->all()], 422);
        // }
        $update=User::find($id);


            
        $update->name = $request->name; 
        $update->address = $request->address;
        $update->city = $request->city;
        $update->state = $request->state;
        $update->country = $request->country;
        $update->postal_code = $request->postal_code;
        $update->phone = $request->phone;
        $update->save();
            
        $shop = Shop::where('seller_id',$update->id)->first();
        $shop->name = $request->shop_name;
        $shop->address = $request->shop_address;
        if($request->file('logo'))
        {
                $file= $request->logo;
                $filename= date('YmdHis').$file->getClientOriginalName();
                $file->storeAs('public', $filename);
                $shop->logo = $filename;
        }
        $shop->save();


        // $BusineesInformation = BusinessInformation::where('seller_id',$update->id)->first();
        // $BusineesInformation->first_name = $request->business_first_name;
        // $BusineesInformation->last_name = $request->business_last_name;
        // $BusineesInformation->business_name = $request->business_name;
        // $BusineesInformation->ein_number = $request->ein_number;
        // $BusineesInformation->address1 = $request->address1;
        // $BusineesInformation->address2 = $request->address2;
        // $BusineesInformation->zip_code = $request->business_zip_code;
        // $BusineesInformation->country = $request->business_country;
        // $BusineesInformation->state = $request->business_state;
        // $BusineesInformation->city = $request->business_city;
        // $BusineesInformation->phone_number = $request->business_phone_number;
        // $BusineesInformation->business_email = $request->business_email;
        // $BusineesInformation->save();
        
        
        if($request->selling_platforms != null)
        {
            foreach($request->selling_platforms as $items)
            {
                if (isset($items['selling_platform_name']) && isset($items['selling_platform_link'])) {
                    $SellingPlatforms = SellingPlatforms::where('seller_id',$update->id)->first();
                    if(!$SellingPlatforms)
                    {
                        $SellingPlatforms = new SellingPlatforms();
                    }
                    $SellingPlatforms->seller_id = $update->id;
                    $SellingPlatforms->name = $items['selling_platform_name'];
                    $SellingPlatforms->link = $items['selling_platform_link'];
                    $SellingPlatforms->save();
                }
            }
        }

        if($request->social_platforms != null)
        {
            foreach($request->social_platforms as $items)
            {
                if (isset($items['social_platform_name']) && isset($items['social_platform_link'])) {
                    $SocialPlatforms = SocialPlatforms::where('seller_id',$update->id)->first();
            
                    if(!$SocialPlatforms)
                    {
                        $SocialPlatforms = new SocialPlatforms();
                    }
                    $SocialPlatforms->seller_id = $update->id;
                    $SocialPlatforms->name = $items['social_platform_name'];
                    $SocialPlatforms->link = $items['social_platform_link'];
                    $SocialPlatforms->save();
                }
            }
        }


//         $BankDetail = BankDetail::where('seller_id',$update->id)->first();
//         if(!$BankDetail)
//         {
//             $BankDetail = new BankDetail();
//         }
//         $BankDetail->seller_id = $update->id;
//         $BankDetail->business_name = $BusineesInformation->business_name;
//         $BankDetail->bank_name = $request->bank_name;
//         $BankDetail->account_title = $request->account_title;
//         $BankDetail->routing_number = $request->routing_number;
//         $BankDetail->account_number = $request->account_number;
//         $BankDetail->save();
//         $CreditCard = CreditCard::where('seller_id',$update->id)->first();
//         if(!$CreditCard)
//         {
//             $CreditCard = new CreditCard();
//         }
//         $CreditCard->seller_id = $update->id;
//         $CreditCard->name_on_card = $request->name_on_card;
//         $CreditCard->cc_number = $request->cc_number;
//         $CreditCard->exp_date = $request->exp_date;
//         $CreditCard->cvv = $request->cvv;
//         $CreditCard->zip_code = $request->card_zip_code;
//         $CreditCard->save();




// // Update Stripe account
// Stripe::setApiKey(config('services.stripe.secret'));

// try {
//     $account = Account::retrieve($update->stripe_account_id);
//     $account->email = $request->email;
//     $account->individual->first_name = $request->business_first_name;
//     $account->individual->last_name = $request->business_last_name;
//     $account->individual->id_number = $request->ssn;
//     $account->individual->dob->day = $request->business_date;
//     $account->individual->dob->month = $request->business_month;
//     $account->individual->dob->year = $request->business_year;
//     $account->individual->ssn_last_4 = $request->last_4_ssn;
//     $account->individual->address->line1 = $request->address1;
//     $account->individual->address->line2 = $request->address2;
//     $account->individual->address->city = $request->business_city;
//     $account->individual->address->state = $request->business_state;
//     $account->individual->address->postal_code = $request->business_zip_code;
//     $account->individual->address->country = $request->business_country;
//     $account->save();

//     $bankAccount = $account->external_accounts->retrieve($seller->bank_account_id);
//     $bankAccount->account_holder_name = $request->account_title;
//     $bankAccount->routing_number = $request->routing_number;
//     $bankAccount->account_number = $request->account_number;
//     $bankAccount->save();
// } catch (\Exception $e) {
//     return response()->json(['status' => 422, 'message' => $e->getMessage()]);
// }

        $response = ['status'=>true,"message" => "profile updated successfully!"];
        return response($response, 200);
    }

    public function passwordChange(Request $request){
        $controlls = $request->all();
        $id=$request->id;
        $rules = array(
            "old_password" => "required",
            "new_password" => "required|required_with:confirm_password|same:confirm_password",
            "confirm_password" => "required"
        );

        $validator = Validator::make($controlls, $rules);
        if ($validator->fails()) {
            //return redirect()->back()->withErrors($validator)->withInput($controlls);
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('id',$request->id)->first();
        $hashedPassword = $user->password;
 
        if(Hash::check($request->old_password , $hashedPassword )) {
 
            if (!Hash::check($request->new_password , $hashedPassword)) {
                $users = User::find($request->id);
                $users->password = bcrypt($request->new_password);
                $users->save();
                $response = ['status'=>true,"message" => "Password Changed Successfully"];
                return response($response, 200);
            }else{
                $response = ['status'=>true,"message" => "new password can not be the old password!"];
                return response($response, 422);
            }
 
        }else{
            $response = ['status'=>true,"message" => "old password does not matched"];
            return response($response, 422);
        }

    }

    public function delete_selling_platforms($id)
    {
        SellingPlatforms::find($id)->delete();

        $response = ['status'=>true,"message" => "deleted successfully"];
        return response($response, 200);
    }


    public function delete_social_platforms($id)
    {
        SocialPlatforms::find($id)->delete();

        $response = ['status'=>true,"message" => "deleted successfully"];
        return response($response, 200);
    }
}
