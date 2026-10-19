document.querySelectorAll('.profile-card').forEach(card => {
  card.addEventListener('mouseenter', () => card.style.transform='translateY(-5px)');
  card.addEventListener('mouseleave', () => card.style.transform='translateY(0)');
});