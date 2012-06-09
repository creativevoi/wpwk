<h1>Personal details</h1>
<p>Please note we will <strong>never</strong> share or sell any of your personal information.
All information provided is encrypted for security and privacy and automatically deleted after 30 days.</p>

<form method="post" action="<?php echo admin_url('admin-ajax.php');?>">
    <ul class="form">
        <li>
            <label><strong>What's your name?</strong> <span>*</span></label>
            <div class="fields">
                <input type="text" name="firstname" id="firstname" value="First name" class="txt" />
                <input type="text" name="lastname" id="lastname" value="Last name" class="txt" />
                <?php echo $this->helpers->infoimg('Example: John Smith or Jane Smith')?>
            </div>
        </li>
        <li>
            <label><strong>What's your email address?</strong> <span>*</span></label>
            <div class="fields">
                <input type="text" name="email" id="email" value="" class="txt" />
                <?php echo $this->helpers->infoimg('This is where your completed Will is emailed - We will never share or sell your email address.')?>
            </div>
        </li>
        <li>
            <label><strong>What's your occupation?</strong> <span>*</span></label>
            <div class="fields">
                <input type="text" name="occupation" id="occupation" value="" class="txt" />
                <?php echo $this->helpers->infoimg('Example: Accountant, Book Keeper, Retired, Home Maker, Plumber,...')?>
            </div>
        </li>
        <li>
            <label for="dob_day"><strong>Date of birth</strong> <span>*</span></label>
            <div class="fields">
                <?php echo $this->helpers->selectbox_days('dob_day', '', 'id="dob_day"')?> -
                <?php echo $this->helpers->selectbox_months('dob_month', '', 'id="dob_month"')?> -
                <?php echo $this->helpers->selectbox_years('dob_year', '', 'id="dob_year"')?>
            </div>
        </li>
        <li>
            <label><strong>Street address</strong> <span>*</span></label>
            <div class="fields">
                <input type="text" name="address" id="address" value="" class="txt" />
                <?php echo $this->helpers->infoimg('Example: Unit 12, 62 George Street')?>
            </div>
        </li>
        <li>
            <label for="city"><strong>Suburb/City/Town</strong> <span>*</span></label>
            <div class="fields">
                <input type="text" name="city" id="city" value="" class="txt" />
                <?php echo $this->helpers->selectbox_states('state', '', 'id="state"')?>
            </div>
        </li>
    </ul>
</form>