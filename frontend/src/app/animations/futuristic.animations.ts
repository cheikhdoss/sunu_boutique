import { trigger, state, style, transition, animate, keyframes, query, stagger, group } from '@angular/animations';

export const futuristicAnimations = [
  // Animation d'apparition avec effet de glitch
  trigger('glitchFadeIn', [
    transition(':enter', [
      style({ 
        opacity: 0, 
        transform: 'translateY(30px) scale(0.9)',
        filter: 'blur(5px)'
      }),
      animate('600ms cubic-bezier(0.25, 0.46, 0.45, 0.94)', 
        style({ 
          opacity: 1, 
          transform: 'translateY(0) scale(1)',
          filter: 'blur(0px)'
        })
      )
    ])
  ]),

  // Animation de carte avec effet holographique
  trigger('holographicCard', [
    state('normal', style({
      transform: 'perspective(1000px) rotateX(0deg) rotateY(0deg)',
      boxShadow: '0 4px 20px rgba(63, 164, 238, 0.2)'
    })),
    state('hovered', style({
      transform: 'perspective(1000px) rotateX(-5deg) rotateY(5deg) translateY(-10px)',
      boxShadow: '0 20px 40px rgba(63, 164, 238, 0.4), 0 0 20px rgba(233, 30, 99, 0.3)'
    })),
    transition('normal <=> hovered', animate('300ms cubic-bezier(0.4, 0, 0.2, 1)'))
  ]),

  // Animation de pulsation néon
  trigger('neonPulse', [
    transition(':enter', [
      animate('2s ease-in-out infinite', keyframes([
        style({ boxShadow: '0 0 5px rgba(63, 164, 238, 0.5)', offset: 0 }),
        style({ boxShadow: '0 0 20px rgba(63, 164, 238, 0.8), 0 0 30px rgba(233, 30, 99, 0.6)', offset: 0.5 }),
        style({ boxShadow: '0 0 5px rgba(63, 164, 238, 0.5)', offset: 1 })
      ]))
    ])
  ]),

  // Animation de slide avec effet de particules
  trigger('particleSlide', [
    transition(':enter', [
      style({ 
        opacity: 0, 
        transform: 'translateX(-100px)',
        filter: 'brightness(0.5)'
      }),
      group([
        animate('500ms cubic-bezier(0.25, 0.46, 0.45, 0.94)', 
          style({ 
            opacity: 1, 
            transform: 'translateX(0)',
            filter: 'brightness(1)'
          })
        ),
        animate('500ms ease-out', keyframes([
          style({ boxShadow: '0 0 0 rgba(63, 164, 238, 0)', offset: 0 }),
          style({ boxShadow: '0 0 20px rgba(63, 164, 238, 0.6)', offset: 0.7 }),
          style({ boxShadow: '0 0 0 rgba(63, 164, 238, 0)', offset: 1 })
        ]))
      ])
    ])
  ]),

  // Animation de rotation 3D
  trigger('rotate3D', [
    transition(':enter', [
      style({ 
        transform: 'perspective(1000px) rotateY(-90deg)',
        opacity: 0
      }),
      animate('600ms cubic-bezier(0.25, 0.46, 0.45, 0.94)', 
        style({ 
          transform: 'perspective(1000px) rotateY(0deg)',
          opacity: 1
        })
      )
    ])
  ]),

  // Animation de morphing
  trigger('morphing', [
    state('initial', style({
      borderRadius: '12px',
      transform: 'scale(1)'
    })),
    state('morphed', style({
      borderRadius: '50px',
      transform: 'scale(1.05)'
    })),
    transition('initial <=> morphed', animate('400ms cubic-bezier(0.4, 0, 0.2, 1)'))
  ]),

  // Animation de liste avec stagger
  trigger('staggerList', [
    transition('* => *', [
      query(':enter', [
        style({ 
          opacity: 0, 
          transform: 'translateY(30px) scale(0.8)',
          filter: 'blur(3px)'
        }),
        stagger('100ms', [
          animate('500ms cubic-bezier(0.25, 0.46, 0.45, 0.94)', 
            style({ 
              opacity: 1, 
              transform: 'translateY(0) scale(1)',
              filter: 'blur(0px)'
            })
          )
        ])
      ], { optional: true })
    ])
  ]),

  // Animation de bouton futuriste
  trigger('futuristicButton', [
    state('normal', style({
      transform: 'scale(1)',
      boxShadow: '0 4px 15px rgba(63, 164, 238, 0.3)'
    })),
    state('pressed', style({
      transform: 'scale(0.95)',
      boxShadow: '0 2px 10px rgba(63, 164, 238, 0.6), inset 0 2px 10px rgba(0, 0, 0, 0.1)'
    })),
    state('hovered', style({
      transform: 'scale(1.05) translateY(-2px)',
      boxShadow: '0 8px 25px rgba(63, 164, 238, 0.5), 0 0 20px rgba(233, 30, 99, 0.3)'
    })),
    transition('normal <=> hovered', animate('200ms cubic-bezier(0.4, 0, 0.2, 1)')),
    transition('normal <=> pressed', animate('100ms cubic-bezier(0.4, 0, 0.2, 1)'))
  ]),

  // Animation de chargement cyberpunk
  trigger('cyberpunkLoader', [
    transition(':enter', [
      animate('1.5s ease-in-out infinite', keyframes([
        style({ 
          transform: 'rotate(0deg) scale(1)',
          borderColor: 'rgba(63, 164, 238, 1)',
          offset: 0 
        }),
        style({ 
          transform: 'rotate(180deg) scale(1.1)',
          borderColor: 'rgba(233, 30, 99, 1)',
          offset: 0.5 
        }),
        style({ 
          transform: 'rotate(360deg) scale(1)',
          borderColor: 'rgba(63, 164, 238, 1)',
          offset: 1 
        })
      ]))
    ])
  ]),

  // Animation de texte néon
  trigger('neonText', [
    transition(':enter', [
      style({ 
        opacity: 0,
        textShadow: 'none'
      }),
      animate('800ms ease-out', 
        style({ 
          opacity: 1,
          textShadow: '0 0 10px rgba(63, 164, 238, 0.8), 0 0 20px rgba(63, 164, 238, 0.6), 0 0 30px rgba(63, 164, 238, 0.4)'
        })
      )
    ])
  ]),

  // Animation de scan line
  trigger('scanLine', [
    transition(':enter', [
      animate('2s linear infinite', keyframes([
        style({ 
          background: 'linear-gradient(90deg, transparent 0%, rgba(63, 164, 238, 0.3) 50%, transparent 100%)',
          transform: 'translateX(-100%)',
          offset: 0 
        }),
        style({ 
          background: 'linear-gradient(90deg, transparent 0%, rgba(63, 164, 238, 0.3) 50%, transparent 100%)',
          transform: 'translateX(100%)',
          offset: 1 
        })
      ]))
    ])
  ])
];

// Animations CSS personnalisées pour les effets avancés
export const futuristicCSS = `
  @keyframes matrix-rain {
    0% { transform: translateY(-100vh); opacity: 1; }
    100% { transform: translateY(100vh); opacity: 0; }
  }

  @keyframes hologram-flicker {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
    75% { opacity: 0.9; }
  }

  @keyframes energy-pulse {
    0% { 
      box-shadow: 0 0 5px rgba(63, 164, 238, 0.5);
      border-color: rgba(63, 164, 238, 0.3);
    }
    50% { 
      box-shadow: 0 0 20px rgba(63, 164, 238, 0.8), 0 0 30px rgba(233, 30, 99, 0.6);
      border-color: rgba(233, 30, 99, 0.8);
    }
    100% { 
      box-shadow: 0 0 5px rgba(63, 164, 238, 0.5);
      border-color: rgba(63, 164, 238, 0.3);
    }
  }

  @keyframes data-stream {
    0% { 
      background-position: 0% 0%;
      opacity: 0.3;
    }
    50% { 
      background-position: 100% 100%;
      opacity: 0.7;
    }
    100% { 
      background-position: 0% 0%;
      opacity: 0.3;
    }
  }

  @keyframes cyber-glitch {
    0%, 100% { 
      transform: translate(0);
      filter: hue-rotate(0deg);
    }
    10% { 
      transform: translate(-2px, 2px);
      filter: hue-rotate(90deg);
    }
    20% { 
      transform: translate(-2px, -2px);
      filter: hue-rotate(180deg);
    }
    30% { 
      transform: translate(2px, 2px);
      filter: hue-rotate(270deg);
    }
    40% { 
      transform: translate(2px, -2px);
      filter: hue-rotate(360deg);
    }
  }

  .matrix-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    background: linear-gradient(45deg, 
      rgba(63, 164, 238, 0.03) 0%, 
      rgba(233, 30, 99, 0.03) 100%);
  }

  .hologram-effect {
    position: relative;
    animation: hologram-flicker 3s ease-in-out infinite;
  }

  .hologram-effect::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
      90deg,
      transparent 0%,
      rgba(63, 164, 238, 0.1) 50%,
      transparent 100%
    );
    animation: scanLine 2s linear infinite;
    pointer-events: none;
  }

  .energy-border {
    border: 2px solid rgba(63, 164, 238, 0.3);
    animation: energy-pulse 2s ease-in-out infinite;
  }

  .data-stream-bg {
    background: linear-gradient(
      45deg,
      rgba(63, 164, 238, 0.1) 0%,
      rgba(233, 30, 99, 0.1) 25%,
      rgba(63, 164, 238, 0.1) 50%,
      rgba(233, 30, 99, 0.1) 75%,
      rgba(63, 164, 238, 0.1) 100%
    );
    background-size: 400% 400%;
    animation: data-stream 4s ease-in-out infinite;
  }

  .cyber-glitch {
    animation: cyber-glitch 0.3s ease-in-out infinite;
  }

  .neon-glow {
    text-shadow: 
      0 0 5px rgba(63, 164, 238, 0.8),
      0 0 10px rgba(63, 164, 238, 0.6),
      0 0 15px rgba(63, 164, 238, 0.4),
      0 0 20px rgba(63, 164, 238, 0.2);
  }

  .particle-effect {
    position: relative;
    overflow: hidden;
  }

  .particle-effect::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
      radial-gradient(circle at 20% 20%, rgba(63, 164, 238, 0.3) 1px, transparent 1px),
      radial-gradient(circle at 80% 80%, rgba(233, 30, 99, 0.3) 1px, transparent 1px),
      radial-gradient(circle at 40% 60%, rgba(63, 164, 238, 0.2) 1px, transparent 1px);
    background-size: 50px 50px, 30px 30px, 70px 70px;
    animation: matrix-rain 10s linear infinite;
    pointer-events: none;
  }
`;