<?php defined('WPINC') || die; ?>

<div class="glsr-review ACCORDION_ITEM accordion-item accordion-closed">
	<h3 class="accordion-toggle ACCORDION_TOGGLE">Review by {{ author }}<span class="date"> - {{ date }}</span></h3>
	<div class="accordion-content">
		<div class="accordion-content-inner">
			<div class="ratings">
				<div class="rating-container">
					<h4>Overall Rating</h4>
					<div class="rating">{{ overall_rating }}</div>
				</div>
				<div class="rating-container">
					<h4>Premise</h4>
					<div class="rating">{{ premise }}</div>
				</div>
				<div class="rating-container">
					<h4>Plot</h4>
					<div class="rating">{{ plot }}</div>
				</div>
				<div class="rating-container">
					<h4>Character</h4>
					<div class="rating">{{ character }}</div>
				</div>
				<div class="rating-container">
					<h4>Dialogue</h4>
					<div class="rating">{{ dialogue }}</div>
				</div>
				<div class="rating-container">
					<h4>Setting</h4>
					<div class="rating">{{ setting }}</div>
				</div>
			</div>
			<div class="text-container">
				<h4>Era</h4>
				<div class="text-box">{{ era }}</div>
			</div>
			<div class="text-container">
				<h4>Locations</h4>
				<div class="text-box">{{ locations }}</div>
			</div>
			<div class="text-container">
				<h4>Logline</h4>
				<div class="text-box">{{ logline }}</div>
			</div>
			<div class="text-container">
				<h4>Strengths</h4>
				<div class="text-box">{{ strengths }}</div>
			</div>
			<div class="text-container">
				<h4>Weaknesses</h4>
				<div class="text-box">{{ weaknesses }}</div>
			</div>
		</div>
	</div>
</div>
