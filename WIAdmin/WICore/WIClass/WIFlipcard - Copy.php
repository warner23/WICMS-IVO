<?php
#[\AllowDynamicProperties]
/**
* Flipcard Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIFlipcard
{
    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

    }

    public function Flipcard($product)
    {
        echo '<div class="item  col-xs-12 col-lg-12 col-md-12 col-sm-12">
        <div class="flipcard-' . $product['prod_id'] . '">
        <span class="btn-edit editing-' . $product['prod_id'] . '">Edit</span>
        <span class="btn-edit show-delete" onclick="WIProduct.delete(`' . $product['prod_id'] . '`);">Delete</span>
        <div class="back">';
            self::backSideFlip($product);
         echo '</div>

                  
        
         <div class="front text-center">';
            self::frontSideFlip($product);
          echo '</div>
              </div>
                </div>
              <style>
               .editing-' . $product['prod_id'] . '{
                        background-color: #bf6cda;
                      color: white;
                      perspective: 638px;
                      margin-left: 10px;
                      margin-top: 10px;
                      cursor:pointer;
                  }
                    .flipcard-' . $product['prod_id'] . ' {
                      position: relative;
                      width: 100%;
                      height: 275px;
                      perspective: 638px;
                      margin: 0px 6px 10px 0px;
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .front {
                    transform: rotateY(180deg);
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .back {
                    transform: rotateY(0deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .back{
                    transform: rotateY(-180deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .front, .flipcard-' . $product['prod_id'] . ' .back
                  {
                      background-color: #eae8e8;
                      border: 1px solid #222;
                      border-radius: 5px;
                      box-shadow: 0 5px 15px rgba(0,0,0,.5);
                      position: absolute;
                      width: 100%;
                      height: 100%;
                      box-sizing: border-box;
                      transition: all 1s ease 0s;
                      color: #a94442;
                      padding: 10px 5px;
                      backface-visibility: hidden;
                      top: 5px;
                      z-index: -1;
                  }
                    </style>
                              <script type="text/javascript">
                              $(document).ready(function(){

                               $(".editing-' . $product['prod_id'] . '").on("click", function(event) {
                                event.stopPropagation();
                                $(".flipcard-' . $product['prod_id'] . '").toggleClass("flip");
                                          });

                              var obj = $("#dragandrophandler-' . $product['prod_id'] . '");
                              var dir = $(".supload").attr("value");
                              var ele_id = $(".img-preview-' . $product['prod_id'] . '").attr("prod_id");

                            obj.on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                                $(this).css("border", "2px solid #0B85A1");
                            });
                            obj.on("dragover", function (e) 
                            {
                                 e.stopPropagation();
                                 e.preventDefault();
                            });
                            obj.on("drop", function (e)
                            {
                             
                                 $(this).css("border", "2px dotted #0B85A1");
                                 e.preventDefault();
                                 var files = e.originalEvent.dataTransfer.files;
                                 //We need to send dropped files to Server
                                 showCreationhandleFileUpload(files,obj, dir, ele_id);
                            });
                            $(document).on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });
                            $(document).on("dragover", function (e)
                            {
                            e.stopPropagation();
                              e.preventDefault();
                              obj.css("border", "2px dotted #0B85A1");
                            });
                            $(document).on("drop", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });

                            });
                            </script>';
    }

        public function FlipcardExtras($product)
    {
        echo '<div class="item  col-xs-12 col-lg-12 col-md-12 col-sm-12">
        <div class="flipcard-' . $product['prod_id'] . '">
        <span class="btn-edit editing-' . $product['prod_id'] . '">Edit</span>
        <span class="btn-edit show-delete" onclick="WIProduct.delete(`' . $product['prod_id'] . '`);">Delete</span>
        <div class="back">';
            self::backSideFlipExtras($product);
         echo '</div>

                  
        
         <div class="front text-center">';
            self::frontSideFlipExtras($product);
          echo '</div>
              </div>
                </div>
              <style>
               .editing-' . $product['prod_id'] . '{
                        background-color: #bf6cda;
                      color: white;
                      perspective: 638px;
                      margin-left: 10px;
                      margin-top: 10px;
                      cursor:pointer;
                  }
                    .flipcard-' . $product['prod_id'] . ' {
                      position: relative;
                      width: 100%;
                      height: 275px;
                      perspective: 638px;
                      margin: 0px 6px 10px 0px;
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .front {
                    transform: rotateY(180deg);
                  }
                  .flipcard-' . $product['prod_id'] . '.flip .back {
                    transform: rotateY(0deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .back{
                    transform: rotateY(-180deg);
                  }
                  .flipcard-' . $product['prod_id'] . ' .front, .flipcard-' . $product['prod_id'] . ' .back
                  {
                      background-color: #eae8e8;
                      border: 1px solid #222;
                      border-radius: 5px;
                      box-shadow: 0 5px 15px rgba(0,0,0,.5);
                      position: absolute;
                      width: 100%;
                      height: 100%;
                      box-sizing: border-box;
                      transition: all 1s ease 0s;
                      color: #a94442;
                      padding: 10px 5px;
                      backface-visibility: hidden;
                      top: 5px;
                      z-index: -1;
                  }
                    </style>
                              <script type="text/javascript">
                              $(document).ready(function(){

                               $(".editing-' . $product['prod_id'] . '").on("click", function(event) {
                                event.stopPropagation();
                                $(".flipcard-' . $product['prod_id'] . '").toggleClass("flip");
                                          });

                              var obj = $("#dragandrophandler-' . $product['prod_id'] . '");
                              var dir = $(".supload").attr("value");
                              var ele_id = $(".img-preview-' . $product['prod_id'] . '").attr("prod_id");

                            obj.on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                                $(this).css("border", "2px solid #0B85A1");
                            });
                            obj.on("dragover", function (e) 
                            {
                                 e.stopPropagation();
                                 e.preventDefault();
                            });
                            obj.on("drop", function (e)
                            {
                             
                                 $(this).css("border", "2px dotted #0B85A1");
                                 e.preventDefault();
                                 var files = e.originalEvent.dataTransfer.files;
                                 //We need to send dropped files to Server
                                 showCreationhandleFileUpload(files,obj, dir, ele_id);
                            });
                            $(document).on("dragenter", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });
                            $(document).on("dragover", function (e)
                            {
                            e.stopPropagation();
                              e.preventDefault();
                              obj.css("border", "2px dotted #0B85A1");
                            });
                            $(document).on("drop", function (e) 
                            {
                                e.stopPropagation();
                                e.preventDefault();
                            });

                            });
                            </script>';
    }

    public function frontSideFlip($product)
    {

        echo '<div class="front text-center">
         <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 food-image">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload img-responsive product">
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name">' . $product['prod_name'] . '</span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '' . $product['prod_price'] . '</span>
                

              </h3>
              <p class="food-ingredients">
                ' . $product['prod_desc'] . '
              </p>
            </div>
          </div>
            </div>
              </div>';
    }

    public function backSideFlip($product)
    {
        echo ' <div class="back">
           <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-2 col-md-4 col-sm-4 food-image" id="product_pic' . $product['prod_id'] . '">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload product cp"
                id="productPic' . $product['prod_id'] . '">
                <span><a href="javascript:void(0);" onclick="WIMedia.changeProductPic(`' . $product['prod_id'] . '`)">Change PHoto</a></span>
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name"><input type="text" id="prod_name' . $product['prod_id'] . '" value="' . $product['prod_name'] . '"></span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '<input type="text" id="prod_price' . $product['prod_id'] . '" value="' . $product['prod_price'] . '"></span>
               
              </h3>
              <p class="food-ingredients">
                <textarea id="prod_desc' . $product['prod_id'] . '" value="'. $product['prod_desc'] . '">'. $product['prod_desc'] . '</textarea>
              </p>
            </div>
            <a href="javascript:void(0);" id="productSave" onclick="WIProduct.saveProduct(`' . $product['prod_id'] . '`)">Save</a>
          </div>
            </div>
                  </div>';
    }


    public function frontSideFlipExtras($product)
    {
      //var_dump($product);
        echo '<div class="front text-center">
         <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-4 col-md-4 col-sm-4 food-image">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload img-responsive product">
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">';
             $results = $this->WIdb->select('SELECT * FROM `wipos_extras` WHERE `prod_id`=:id', array("id" => $product['prod_id']));
             //var_dump($results);
             echo '<ul>';
             foreach($results as $res){
              echo '<li>' . $res['name'] . '</li>';
             }
            echo '</div>
          </div>
            </div>
              </div>';
    }

    public function backSideFlipExtras($product)
    {
        echo ' <div class="back">
           <div class="item">
            <div class="row align-items-center menu-item">
            <div class="col-xs-4 col-lg-2 col-md-4 col-sm-4 food-image" id="product_pic' . $product['prod_id'] . '">
              <img 
                src="WIMedia/Img/pos/products/' . $product['prod_img'] . '"
                alt="' . $product['alt'] . '"
                class="rounded-circle lazyload product cp"
                id="productPic' . $product['prod_id'] . '">
                <span><a href="javascript:void(0);" onclick="WIMedia.changeProductPic(`' . $product['prod_id'] . '`)">Change PHoto</a></span>
            </div>
            <div class="col-xs-8 col-lg-8 col-md-8 col-sm-8">
              <h3 class="food-title">
                <span class="food-name"><input type="text" id="prod_name' . $product['prod_id'] . '" value="' . $product['prod_name'] . '"></span>
                <span class="food-price float-right">
                ' .CURRENCY_SYMBOL . '<input type="text" id="prod_price' . $product['prod_id'] . '" value="' . $product['prod_price'] . '"></span>
               
              </h3>
              <p class="food-ingredients">
                <textarea id="prod_desc' . $product['prod_id'] . '" value="'. $product['prod_desc'] . '">'. $product['prod_desc'] . '</textarea>
              </p>
            </div>
            <a href="javascript:void(0);" id="productSave" onclick="WIProduct.saveProduct(`' . $product['prod_id'] . '`)">Save</a>
          </div>
            </div>
                  </div>';
    }








             /**
     * Render a reusable WIFlipcard for a compliance site.
     *
     * Front side = show/read-only.
     * Back side  = edit.
     *
     * Buttons are inside the card faces:
     * - Front top: Edit left, Delete right
     * - Back top: Cancel left, Delete right
     * - Back bottom: Save
     *
     * @param array $site Prepared site data from WIComplianceSites.
     *
     * @return void
     */
    public function FlipcardSites(array $site): void
    {
        $siteId = (int)($site['org_site_id'] ?? $site['id'] ?? 0);

        if ($siteId <= 0) {
            return;
        }

        echo '<div class="item col-xs-12 col-sm-12 col-md-6 col-lg-4 wi-site-card-wrap">
            <div class="flipcard-site-' . $siteId . ' wi-site-flipcard" data-site-id="' . $siteId . '">

                <div class="back">';
                    $this->backSideFlipSites($site);
                echo '</div>

                <div class="front text-center">';
                    $this->frontSideFlipSites($site);
                echo '</div>

            </div>
        </div>';

        echo $this->siteFlipcardStyle($siteId);
        echo $this->siteFlipcardScript($siteId);
    }


    /**
     * Render front/show side of a site flipcard.
     *
     * @param array $site Prepared site data.
     *
     * @return void
     */
    public function frontSideFlipSites(array $site): void
    {
        $siteId       = (int)($site['org_site_id'] ?? $site['id'] ?? 0);
        $siteName     = $this->wiFlipEsc($site['site_name'] ?? 'Unnamed Site');
        $siteType     = $this->wiFlipEsc($site['site_type'] ?? 'Site');
        $trafficState = strtolower((string)($site['traffic_state'] ?? 'grey'));
        $status       = $this->wiFlipEsc($site['status'] ?? 'active');

        echo '<div class="site-front-content" data-site-view="' . $siteId . '">

            <div class="wi-site-card-actions-top">
                <button type="button"
                    class="btn btn-sm wi-site-btn wi-site-btn--edit editing-site-' . $siteId . '"
                    data-site-action="flip"
                    data-site-id="' . $siteId . '">
                    <i class="fa fa-pencil"></i> Edit
                </button>

                <button type="button"
                    class="btn btn-sm wi-site-btn wi-site-btn--delete"
                    data-site-action="archive"
                    data-site-id="' . $siteId . '">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>

            <div class="top-bit">
                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h3 class="siteName">
                            ' . $siteName . '
                        </h3>

                        <p class="wi-site-address-line">
                            <i class="fa fa-map-marker"></i>
                            ' . $this->siteAddressLine($site) . '
                        </p>
                    </div>

                    <div class="col-xs-12 col-sm-4 text-right">
                        <span class="wi-site-status wi-site-status--' . $this->wiFlipEsc($trafficState) . '">
                            ' . ucfirst($status) . '
                            <span></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="middle-bit-site">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        ' . $this->siteMapHtml($site) . '
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        ' . $this->siteReadOnlyFeaturesHtml($site) . '
                    </div>

                    <div class="col-xs-12">
                        ' . $this->siteReadOnlyPremisesFlagsHtml($site) . '
                    </div>

                    <div class="col-xs-12">
                        ' . $this->siteReadOnlyOpeningHtml($site) . '
                    </div>

                    <div class="col-xs-12">
                        ' . $this->siteReadOnlyLinksHtml($site) . '
                    </div>
                </div>
            </div>

            <div class="end-bit">
                <div class="row">
                    <div class="col-xs-12">
                        <button type="button"
                            class="btn btn-sm wi-site-btn wi-site-btn--ghost"
                            data-site-action="inspector"
                            data-site-id="' . $siteId . '">
                            Inspector Mode
                        </button>
                    </div>
                </div>
            </div>

        </div>';
    }


    /**
     * Render back/edit side of a site flipcard.
     *
     * @param array $site Prepared site data.
     *
     * @return void
     */
    public function backSideFlipSites(array $site): void
    {
        $siteId       = (int)($site['org_site_id'] ?? $site['id'] ?? 0);
        $siteName     = $this->wiFlipEsc($site['site_name'] ?? '');
        $status       = $this->wiFlipEsc($site['status'] ?? 'active');
        $trafficState = strtolower((string)($site['traffic_state'] ?? 'grey'));

        echo '<div class="site-back-content" data-site-edit-form="' . $siteId . '">

            <div class="wi-site-card-actions-top">
                <button type="button"
                    class="btn btn-sm wi-site-btn wi-site-btn--cancel"
                    data-site-action="cancel"
                    data-site-id="' . $siteId . '">
                    <i class="fa fa-times"></i> Cancel
                </button>

                <button type="button"
                    class="btn btn-sm wi-site-btn wi-site-btn--delete"
                    data-site-action="archive"
                    data-site-id="' . $siteId . '">
                    <i class="fa fa-trash"></i> Delete
                </button>
            </div>

            <div class="top-bit">
                <div class="row">
                    <div class="col-xs-12 col-sm-8">
                        <h3 class="siteName">
                            ' . $siteName . '
                        </h3>

                        <label class="wi-site-inline-field">
                            <span>Address</span>
                            <input type="text"
                                class="form-control name black"
                                name="address_line_1"
                                id="address_line_1-' . $siteId . '"
                                value="' . $this->wiFlipEsc($site['address_line_1'] ?? $this->siteAddressLineRaw($site)) . '"
                                placeholder="Address">
                        </label>
                    </div>

                    <div class="col-xs-12 col-sm-4 text-right">
                        <span class="wi-site-status wi-site-status--' . $this->wiFlipEsc($trafficState) . '">
                            ' . ucfirst($status) . '
                            <span></span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="middle-bit-site">
                <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <legend>Location</legend>
                        ' . $this->siteMapHtml($site) . '
                        ' . $this->siteEditableMapIframeHtml($site) . '
                    </div>

                    <div class="col-xs-12 col-md-4">
                        <legend>Services</legend>
                        ' . $this->siteEditableFeaturesHtml($site) . '
                    </div>

                    <div class="col-xs-12 col-md-4">
                        <legend>Parking</legend>
                        ' . $this->siteEditableParkingHtml($site) . '

                        <legend>Accessibility</legend>
                        ' . $this->siteEditableAccessibilityHtml($site) . '

                        <legend>Notes</legend>
                        <textarea name="site_profile_notes"
                            rows="4"
                            class="form-control name black"
                            placeholder="Site notes">' . $this->wiFlipEsc($site['site_profile_notes'] ?? '') . '</textarea>
                    </div>

                    <div class="col-xs-12">
                        ' . $this->siteEditableOpeningHtml($site) . '
                    </div>

                    <div class="col-xs-12">
                        ' . $this->siteEditableLinksHtml($site) . '
                    </div>
                </div>
            </div>

            <div class="end-bit wi-site-save-area">
                <div class="row">
                    <div class="col-xs-12">
                        <button type="button"
                            class="btn btn-sm wi-site-btn wi-site-btn--save"
                            data-site-action="save"
                            data-site-id="' . $siteId . '">
                            <i class="fa fa-check"></i> Save
                        </button>
                    </div>
                </div>
            </div>

        </div>';
    }


    /**
     * Build a display address line.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteAddressLine(array $site): string
    {
        return $this->wiFlipEsc($this->siteAddressLineRaw($site));
    }


    /**
     * Build a raw address line.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteAddressLineRaw(array $site): string
    {
        $parts = array_filter([
            $site['address_line_1'] ?? '',
            $site['address_line_2'] ?? '',
            $site['town_city'] ?? '',
            $site['postcode'] ?? '',
            $site['country'] ?? '',
        ]);

        if (empty($parts)) {
            $parts = array_filter([
                $site['town_city'] ?? '',
                $site['county_region'] ?? '',
                $site['country_code'] ?? '',
            ]);
        }

        return !empty($parts) ? implode(', ', $parts) : 'Address not configured yet.';
    }


    /**
     * Map display block.
     *
     * @param array $site Site data.
     *
     * @return string
     */
    private function siteMapHtml(array $site): string
    {
        $parts = array_filter([
            $site['site_name'] ?? '',
            $site['address_line_1'] ?? '',
            $site['town_city'] ?? '',
            $site['postcode'] ?? '',
            $site['country'] ?? '',
        ]);

        if (!empty($parts) && defined('GOOGLE_MAP_API')) {
            $query = rawurlencode(implode(',', $parts));

            return '<iframe class="google_mapping wi-site-google-map"
                src="//www.google.com/maps/embed/v1/place?q=' . $query . '&zoom=17&key=' . $this->wiFlipEsc(GOOGLE_MAP_API) . '">
            </iframe>';
        }

        return '<div class="wi-site-map-box">
            <span>Map not configured yet.</span>
        </div>';
    }


    /**
     * Editable Google map iframe/code field shown under the map.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableMapIframeHtml(array $site): string
    {
        $siteId = (int)($site['org_site_id'] ?? $site['id'] ?? 0);

        $parts = array_filter([
            $site['site_name'] ?? '',
            $site['address_line_1'] ?? '',
            $site['town_city'] ?? '',
            $site['postcode'] ?? '',
            $site['country'] ?? '',
        ]);

        $query = !empty($parts)
            ? implode(',', $parts)
            : ($site['map_url'] ?? '');

        $iframe = '<iframe class="google_mapping" src="//www.google.com/maps/embed/v1/place?q='
            . $query .
            '&zoom=17&key=GOOGLE_MAP_API"></iframe>';

        return '<label class="wi-site-code-field">
            <span>Google Map Embed / iframe</span>
            <textarea name="map_url"
                rows="4"
                id="map_url-' . $siteId . '"
                class="form-control name black"
                placeholder="Google Map iframe">' . $this->wiFlipEsc($iframe) . '</textarea>
        </label>';
    }


    /**
     * Read-only features/badges.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteReadOnlyFeaturesHtml(array $site): string
    {
        $featureGroups = $site['feature_groups'] ?? [];

        if (empty($featureGroups)) {
            return '<p class="wi-site-muted">No features configured yet.</p>';
        }

        $html  = '<div class="wi-site-feature-badges">';
        $shown = 0;

        foreach ($featureGroups as $features) {
            foreach ((array)$features as $feature) {
                $code  = (string)($feature['feature_code'] ?? $feature['feature_key'] ?? '');
                $label = (string)($feature['feature_label'] ?? $feature['label'] ?? $code);

                if (!$this->siteFeatureEnabled($site, $code)) {
                    continue;
                }

                $html .= '<span class="wi-site-feature-pill">' . $this->wiFlipEsc($label) . '</span>';
                $shown++;
            }
        }

        if ($shown === 0) {
            $html .= '<span class="wi-site-muted">No enabled features yet.</span>';
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Read-only parking/accessibility row.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteReadOnlyPremisesFlagsHtml(array $site): string
    {
        $parking      = $this->siteFeatureEnabled($site, 'parking');
        $accessibility = $this->siteFeatureEnabled($site, 'accessibility_facilities');

        return '<div class="wi-site-premises-flags">
            <span><strong>P</strong> Parking ' . ($parking ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>') . '</span>
            <span><i class="fa fa-wheelchair"></i> Accessible ' . ($accessibility ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>') . '</span>
        </div>';
    }


    /**
     * Editable features/toggles.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableFeaturesHtml(array $site): string
    {
        $featureGroups = $site['feature_groups'] ?? [];

        if (empty($featureGroups)) {
            return '<p class="wi-site-muted">No feature options available.</p>';
        }

        $preferred = [
            'hotel',
            'restaurant',
            'food_service',
            'alcohol_service',
            'cctv',
            'chilled_storage',
            'staffed_premises',
        ];

        $html = '<div class="wi-site-toggle-list">';

        foreach ($preferred as $wantedCode) {
            $feature = $this->findSiteFeatureDefinition($featureGroups, $wantedCode);

            if ($feature === null) {
                continue;
            }

            $code    = (string)($feature['feature_code'] ?? $feature['feature_key'] ?? $wantedCode);
            $label   = (string)($feature['feature_label'] ?? $feature['label'] ?? $code);
            $enabled = $this->siteFeatureEnabled($site, $code);

            $html .= $this->siteToggleHtml($code, $label, $enabled);
        }

        $html .= '</div>';

        return $html;
    }


    /**
     * Editable parking switch.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableParkingHtml(array $site): string
    {
        return '<div class="wi-site-toggle-list">'
            . $this->siteToggleHtml('parking', 'On-site Parking', $this->siteFeatureEnabled($site, 'parking'), 'P')
            . '</div>';
    }


    /**
     * Editable accessibility switches.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableAccessibilityHtml(array $site): string
    {
        return '<div class="wi-site-toggle-list">'
            . $this->siteToggleHtml('accessibility_facilities', 'Wheelchair Access', $this->siteFeatureEnabled($site, 'accessibility_facilities'), '<i class="fa fa-wheelchair"></i>')
            . $this->siteToggleHtml('accessible_toilet', 'Accessible Toilet', $this->siteFeatureEnabled($site, 'accessible_toilet'), '<i class="fa fa-wheelchair"></i>')
            . '</div>';
    }


    /**
     * Build one toggle row.
     *
     * @param string $code
     * @param string $label
     * @param bool   $enabled
     * @param string $icon
     *
     * @return string
     */
    private function siteToggleHtml(string $code, string $label, bool $enabled, string $icon = ''): string
    {
        return '<label class="wi-site-toggle-row">
            <span class="wi-site-toggle-label">' . $icon . ' ' . $this->wiFlipEsc($label) . '</span>
            <input type="checkbox"
                name="features[' . $this->wiFlipEsc($code) . ']"
                value="1"
                data-feature-code="' . $this->wiFlipEsc($code) . '"
                ' . ($enabled ? 'checked' : '') . '>
            <span class="wi-site-toggle-ui"></span>
        </label>';
    }


    /**
     * Find a feature definition in grouped feature data.
     *
     * @param array  $featureGroups
     * @param string $wantedCode
     *
     * @return array|null
     */
    private function findSiteFeatureDefinition(array $featureGroups, string $wantedCode): ?array
    {
        foreach ($featureGroups as $features) {
            foreach ((array)$features as $feature) {
                $code = (string)($feature['feature_code'] ?? $feature['feature_key'] ?? '');

                if ($code === $wantedCode) {
                    return $feature;
                }
            }
        }

        return null;
    }


    /**
     * Read-only opening times.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteReadOnlyOpeningHtml(array $site): string
    {
        $publicOpen = $this->wiFlipEsc($site['public_open_time'] ?? '');
        $closeTime  = $this->wiFlipEsc($site['close_time'] ?? '');

        $timeText = ($publicOpen !== '' || $closeTime !== '')
            ? trim($publicOpen . ' - ' . $closeTime, ' -')
            : '24 Hours';

        return '<div class="wi-site-opening-row">
            <i class="fa fa-clock-o"></i>
            <strong>Mon – Sun</strong>
            <span>' . $timeText . '</span>
        </div>';
    }


    /**
     * Editable opening times.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableOpeningHtml(array $site): string
    {
        return '<div class="wi-site-edit-strip">
            <div class="wi-site-edit-strip__section wi-site-edit-strip__section--opening">
                <legend>Opening Times</legend>

                <div class="wi-site-opening-controls">
                    <i class="fa fa-clock-o"></i>

                    <select class="form-control name black" name="opening_days">
                        <option value="mon_sun">Mon – Sun</option>
                    </select>

                    <input type="time"
                        name="public_open_time"
                        class="form-control name black"
                        value="' . $this->wiFlipEsc($site['public_open_time'] ?? '') . '">

                    <span>-</span>

                    <input type="time"
                        name="close_time"
                        class="form-control name black"
                        value="' . $this->wiFlipEsc($site['close_time'] ?? '') . '">
                </div>

                <a href="javascript:void(0);" class="wi-site-inline-link">+ Add Time</a>
            </div>

            <div class="wi-site-edit-strip__section">
                <legend>Reviews / Testimonials</legend>
                <p class="wi-site-stars"><i class="fa fa-star"></i> 4.6 (128)</p>
                <a href="javascript:void(0);" class="wi-site-inline-link">Manage reviews</a>
            </div>
        </div>';
    }


    /**
     * Read-only links/local info.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteReadOnlyLinksHtml(array $site): string
    {
        return '<div class="wi-site-info-panels">
            <div>
                <h4>Website</h4>
                <p><i class="fa fa-globe"></i> ' . $this->wiFlipEsc($site['website_url'] ?? 'Not configured') . '</p>

                <h4>Social Media</h4>
                <div class="wi-site-socials">
                    <span>f</span>
                    <span>◎</span>
                    <span>in</span>
                    <span>+</span>
                </div>
            </div>

            <div>
                <h4>Reviews / Testimonials</h4>
                <p class="wi-site-stars"><i class="fa fa-star"></i> 4.6 (128)</p>
                <a href="javascript:void(0);" class="wi-site-inline-link">View all reviews</a>
            </div>

            <div>
                <h4>Nearby Services</h4>
                <p>' . $this->wiFlipEsc($site['nearby_services_notes'] ?? 'Pharmacy (0.2mi)') . '</p>
                <p>ATM (0.3mi)</p>
                <p>Bus Stop (0.2mi)</p>
                <a href="javascript:void(0);" class="wi-site-inline-link">View all nearby</a>
            </div>
        </div>';
    }


    /**
     * Editable links/local info.
     *
     * @param array $site
     *
     * @return string
     */
    private function siteEditableLinksHtml(array $site): string
    {
        return '<div class="wi-site-edit-strip wi-site-edit-strip--bottom">
            <div class="wi-site-edit-strip__section">
                <legend>Website</legend>
                <input type="text"
                    name="website_url"
                    class="form-control name black"
                    value="' . $this->wiFlipEsc($site['website_url'] ?? '') . '"
                    placeholder="Website">
            </div>

            <div class="wi-site-edit-strip__section">
                <legend>Social Media</legend>
                <div class="wi-site-socials">
                    <span>f</span>
                    <span>◎</span>
                    <span>in</span>
                    <span>+</span>
                </div>
            </div>

            <div class="wi-site-edit-strip__section">
                <legend>Nearby Services</legend>
                <input type="text"
                    name="nearby_services_notes"
                    class="form-control name black"
                    value="' . $this->wiFlipEsc($site['nearby_services_notes'] ?? '') . '"
                    placeholder="Nearby services">
                <a href="javascript:void(0);" class="wi-site-inline-link">Manage nearby</a>
            </div>
        </div>';
    }


    /**
     * Checks whether a feature is enabled on the current site payload.
     *
     * @param array  $site
     * @param string $featureCode
     *
     * @return bool
     */
    private function siteFeatureEnabled(array $site, string $featureCode): bool
    {
        $featureCode   = trim($featureCode);
        $savedFeatures = $site['saved_features'] ?? [];

        if ($featureCode === '' || !is_array($savedFeatures) || !isset($savedFeatures[$featureCode])) {
            return false;
        }

        $value = $savedFeatures[$featureCode];

        if (is_array($value)) {
            return !empty($value['enabled']);
        }

        return !empty($value);
    }


    /**
     * Per-card flipcard style.
     *
     * @param int $siteId
     *
     * @return string
     */
    private function siteFlipcardStyle(int $siteId): string
    {
        return '<style>
            .wi-site-card-wrap {
                float: none;
                width: 100%;
                padding: 0;
            }

            .flipcard-site-' . $siteId . ' {
                position: relative;
                width: 100%;
                min-height: 760px;
                height: auto;
                perspective: 638px;
                margin: 0 0 18px 0;
                overflow: visible;
            }

            .flipcard-site-' . $siteId . '.flip {
                min-height: 860px;
            }

            .flipcard-site-' . $siteId . '.flip .front {
                transform: rotateY(180deg);
                z-index: 1;
            }

            .flipcard-site-' . $siteId . '.flip .back {
                transform: rotateY(0deg);
                z-index: 2;
            }

            .flipcard-site-' . $siteId . ' .back {
                transform: rotateY(-180deg);
                z-index: 1;
            }

            .flipcard-site-' . $siteId . ' .front {
                z-index: 2;
            }

            .flipcard-site-' . $siteId . ' .front,
            .flipcard-site-' . $siteId . ' .back {
                background:
                    radial-gradient(circle at top right, rgba(191,108,218,0.16), transparent 36%),
                    linear-gradient(145deg, #080d14 0%, #111821 60%, #151124 100%);
                border: 1px solid rgba(191,108,218,0.62);
                border-radius: 8px;
                box-shadow: 0 10px 30px rgba(0,0,0,.55);
                position: absolute;
                width: 100%;
                min-height: 760px;
                height: auto;
                box-sizing: border-box;
                transition: all 0.8s ease 0s;
                color: white;
                padding: 14px 16px;
                backface-visibility: hidden;
                top: 0;
                left: 0;
                overflow: visible;
            }

            .flipcard-site-' . $siteId . ' .back {
                min-height: 860px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-card-actions-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                width: 100%;
                margin-bottom: 16px;
                padding-bottom: 12px;
                border-bottom: 1px solid rgba(255,255,255,0.10);
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn {
                min-width: 118px;
                border-radius: 6px;
                font-weight: 700;
                padding: 8px 14px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn--edit,
            .flipcard-site-' . $siteId . ' .wi-site-btn--save {
                color: #061016;
                background: #16c9ef;
                border: 1px solid rgba(22,201,239,0.78);
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn--cancel {
                color: #ffffff;
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.28);
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn--delete {
                color: #ff5d66;
                background: rgba(255,93,102,0.08);
                border: 1px solid rgba(255,93,102,0.62);
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn--ghost {
                color: #7ce8ff;
                background: rgba(124,232,255,0.08);
                border: 1px solid rgba(124,232,255,0.25);
            }

            .flipcard-site-' . $siteId . ' .top-bit,
            .flipcard-site-' . $siteId . ' .middle-bit-site,
            .flipcard-site-' . $siteId . ' .end-bit {
                width: 100%;
                float: left;
                clear: both;
                margin-bottom: 14px;
            }

            .flipcard-site-' . $siteId . ' .siteName {
                margin: 0 0 8px;
                color: #ffffff;
                font-size: 22px;
                font-weight: 800;
            }

            .flipcard-site-' . $siteId . ' .wi-site-address-line {
                color: rgba(255,255,255,0.82);
                margin: 0 0 12px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-address-line i {
                color: #7ce8ff;
                margin-right: 6px;
            }

            .flipcard-site-' . $siteId . ' legend {
                color: #ffffff;
                border-bottom: 0;
                font-size: 13px;
                margin: 0 0 8px;
                font-weight: 800;
            }

            .flipcard-site-' . $siteId . ' .black {
                color: #ffffff;
                background: rgba(0,0,0,0.18);
                border: 1px solid rgba(255,255,255,0.16);
                border-radius: 5px;
            }

            .flipcard-site-' . $siteId . ' .form-control {
                min-height: 32px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-inline-field {
                display: flex;
                align-items: center;
                gap: 8px;
                width: 100%;
            }

            .flipcard-site-' . $siteId . ' .wi-site-inline-field span {
                min-width: 55px;
                color: rgba(255,255,255,0.75);
                font-size: 12px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-status {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 7px 13px;
                border-radius: 999px;
                font-size: 13px;
                font-weight: bold;
                background: rgba(47,168,79,0.18);
                color: #d8ffe2;
                border: 1px solid rgba(47,168,79,0.38);
            }

            .flipcard-site-' . $siteId . ' .wi-site-status span {
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background: #43e879;
            }

            .flipcard-site-' . $siteId . ' .google_mapping,
            .flipcard-site-' . $siteId . ' .wi-site-google-map {
                width: 100%;
                min-height: 150px;
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 5px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-map-box {
                min-height: 150px;
                padding: 18px;
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 5px;
                text-align: center;
            }

            .flipcard-site-' . $siteId . ' .wi-site-code-field {
                display: block;
                margin-top: 8px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-code-field span {
                display: block;
                margin-bottom: 4px;
                font-size: 11px;
                color: rgba(255,255,255,0.72);
            }

            .flipcard-site-' . $siteId . ' .wi-site-code-field textarea {
                font-family: Consolas, Monaco, monospace;
                font-size: 11px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-feature-badges {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 4px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-feature-pill {
                display: inline-flex;
                align-items: center;
                padding: 6px 10px;
                border-radius: 6px;
                background: linear-gradient(135deg, rgba(191,108,218,0.45), rgba(116,86,173,0.28));
                border: 1px solid rgba(191,108,218,0.36);
                color: #fff;
                font-size: 12px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-premises-flags {
                display: flex;
                gap: 22px;
                margin: 10px 0;
                color: rgba(255,255,255,0.84);
            }

            .flipcard-site-' . $siteId . ' .wi-site-premises-flags .fa-check {
                color: #43e879;
                margin-left: 4px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-premises-flags .fa-times {
                color: #ff5d66;
                margin-left: 4px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-opening-row {
                display: flex;
                align-items: center;
                gap: 14px;
                padding: 12px;
                margin: 12px 0;
                border: 1px solid rgba(255,255,255,0.13);
                border-radius: 6px;
                background: rgba(255,255,255,0.035);
            }

            .flipcard-site-' . $siteId . ' .wi-site-info-panels,
            .flipcard-site-' . $siteId . ' .wi-site-edit-strip {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                border-top: 1px solid rgba(255,255,255,0.12);
                margin-top: 12px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-info-panels > div,
            .flipcard-site-' . $siteId . ' .wi-site-edit-strip__section {
                padding: 12px;
                border-right: 1px solid rgba(255,255,255,0.10);
            }

            .flipcard-site-' . $siteId . ' .wi-site-info-panels > div:last-child,
            .flipcard-site-' . $siteId . ' .wi-site-edit-strip__section:last-child {
                border-right: 0;
            }

            .flipcard-site-' . $siteId . ' .wi-site-info-panels h4 {
                margin: 0 0 8px;
                color: rgba(255,255,255,0.78);
                font-size: 13px;
                font-weight: 800;
            }

            .flipcard-site-' . $siteId . ' .wi-site-inline-link {
                color: #16c9ef;
                font-size: 12px;
                font-weight: 700;
            }

            .flipcard-site-' . $siteId . ' .wi-site-socials {
                display: flex;
                gap: 8px;
                margin-top: 8px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-socials span {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background: rgba(22,201,239,0.16);
                border: 1px solid rgba(22,201,239,0.30);
                color: #fff;
                font-size: 12px;
                font-weight: 800;
            }

            .flipcard-site-' . $siteId . ' .wi-site-stars .fa-star {
                color: #ffc145;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                color: rgba(255,255,255,0.88);
                font-size: 12px;
                margin: 0;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-row input {
                display: none;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-ui {
                position: relative;
                display: inline-block;
                width: 35px;
                height: 18px;
                background: rgba(255,255,255,0.18);
                border-radius: 999px;
                transition: 0.2s;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-ui:before {
                content: "";
                position: absolute;
                width: 14px;
                height: 14px;
                top: 2px;
                left: 2px;
                border-radius: 50%;
                background: #ffffff;
                transition: 0.2s;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-row input:checked + .wi-site-toggle-ui,
            .flipcard-site-' . $siteId . ' .wi-site-toggle-row input:checked ~ .wi-site-toggle-ui {
                background: #16c9ef;
            }

            .flipcard-site-' . $siteId . ' .wi-site-toggle-row input:checked + .wi-site-toggle-ui:before,
            .flipcard-site-' . $siteId . ' .wi-site-toggle-row input:checked ~ .wi-site-toggle-ui:before {
                transform: translateX(17px);
            }

            .flipcard-site-' . $siteId . ' .wi-site-save-area {
                margin-top: 12px;
                padding-top: 10px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-btn--save {
                min-width: 170px;
            }

            .flipcard-site-' . $siteId . ' .wi-site-muted {
                color: rgba(255,255,255,0.68);
                font-size: 13px;
            }

            @media (max-width: 860px) {
                .flipcard-site-' . $siteId . ' .wi-site-info-panels,
                .flipcard-site-' . $siteId . ' .wi-site-edit-strip {
                    grid-template-columns: 1fr;
                }

                .flipcard-site-' . $siteId . ' .wi-site-info-panels > div,
                .flipcard-site-' . $siteId . ' .wi-site-edit-strip__section {
                    border-right: 0;
                    border-bottom: 1px solid rgba(255,255,255,0.10);
                }
            }
        </style>';
    }


    /**
     * Per-card flip script.
     *
     * @param int $siteId
     *
     * @return string
     */
    private function siteFlipcardScript(int $siteId): string
    {
        return '<script type="text/javascript">
            $(document).ready(function() {
                $(".editing-site-' . $siteId . '").off("click.siteFlip' . $siteId . '").on("click.siteFlip' . $siteId . '", function(event) {
                    event.stopPropagation();
                    $(".flipcard-site-' . $siteId . '").addClass("flip");
                });

                $(".flipcard-site-' . $siteId . ' [data-site-action=\"cancel\"]").off("click.siteCancel' . $siteId . '").on("click.siteCancel' . $siteId . '", function(event) {
                    event.stopPropagation();
                    $(".flipcard-site-' . $siteId . '").removeClass("flip");
                });
            });
        </script>';
    }


    /**
     * Escape output for safe card rendering.
     *
     * @param mixed $value
     *
     * @return string
     */
    private function wiFlipEsc($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
    }



}